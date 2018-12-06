// da installare la libreria libconfig-dev con apt-get install libconfig-dev
// compilare con gcc `mysql_config --cflags` `mysql_config --libs` -lconfig -o hoolyrouter_alive_check hoolyrouter_alive_check.c

// Configuration file in /usr/local/etc/hoolyrouter_alive_check.conf
// # Log file: where hoolyrouter_alive_check will log
// LOGFILE                 = "/var/log/hoolyrouter_alive_check.log"
//
// # Database access informations
// DATABASE_SERVER         = "localhost"
// DATABASE_NAME           = "hooly"
// DATABASE_USERNAME       = "hooly"
// DATABASE_PASSWORD       = "hooly_pwd"
//
// URL for send message
// SENDMESSAGE_URL         = "http://myhooly.hooly.eu/sendmessage.php"
//
//
//# Subject and message body form alarm messages
// SUBJECT                 = "Allarme da Hooly router"
// MESSAGE                 = "Non ricevo dati da oltre 2 minuti"
// EMAIL_FROM              = "hooly@hooly.eu"
//
// # Timeout in seconds for router in alarm
// TIMEOUT                 = 210
//
// # Period in seconds for sample check alive
// PERIOD                  = 15

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <mysql/mysql.h>
#include <time.h>
#include <libconfig.h>
#include <signal.h>

int main(void)
{
  char version[8] = "02";
  const char *LOGFILE;
  const char *DATABASE_SERVER;
  const char *DATABASE_NAME;
  const char *DATABASE_USERNAME;
  const char *DATABASE_PASSWORD;
  const char *SENDMESSAGE_URL;
  const char *SUBJECT;
  const char *MESSAGE;
  const char *EMAIL_FROM;
  int TIMEOUT;
  int PERIOD;

  char query[1024];
  char subquery[512];
  char command[512];
  char router[16];
  char timestamp[64];
  char codUtente[100];
  char telegram_flag[8];
  char telegram_chatid[64];
  char pushbullett_flag[8];
  char pushbullett_addr[128];
  char email_flag[8];
  char email_addr[128];
  char whatsapp_flag[8];
  char whatsapp_tel[16];
  char sms_flag[8];
  char sms_tel[16];
  char sendmessage_key[64];
  char pushbullett_token[128];
  char telegram_BOT_ID[128];
  int counter = 0;

  config_t cf;
  config_init(&cf);

  if (!config_read_file(&cf, "/usr/local/etc/hoolyrouter_alive_check.conf")) {
    fprintf(stderr, "%s:%d - %s\n",
    config_error_file(&cf),
    config_error_line(&cf),
    config_error_text(&cf));
    config_destroy(&cf);
    return(EXIT_FAILURE);
  }

  if (!config_lookup_string(&cf, "LOGFILE", &LOGFILE)) {printf("Manca LOGFILE nella configurazione\n"); exit(0);}

  FILE *logfile = fopen(LOGFILE,"a");
  fprintf(logfile,"\nLooking configuration in /usr/local/etc/hoolyrouter_alive_check.conf\n");

  if (config_lookup_string(&cf, "DATABASE_SERVER", &DATABASE_SERVER))
  fprintf(logfile,"Database Server: %s\n", DATABASE_SERVER); else {printf("Manca DATABASE_SERVER nella configurazione\n"); exit(0);}

  if (config_lookup_string(&cf, "DATABASE_NAME", &DATABASE_NAME))
  fprintf(logfile,"Database name: %s\n", DATABASE_NAME); else {printf("Manca DATABASE_NAME nella configurazione\n"); exit(0);}

  if (config_lookup_string(&cf, "DATABASE_USERNAME", &DATABASE_USERNAME))
  fprintf(logfile,"Database user: ****\n"); else {printf("Manca DATABASE_USERNAME nella configurazione\n"); exit(0);}

  if (config_lookup_string(&cf, "DATABASE_PASSWORD", &DATABASE_PASSWORD))
  fprintf(logfile,"Database password: ****\n"); else {printf("Manca DATABASE_PASSWORD nella configurazione\n"); exit(0);}

  if (config_lookup_string(&cf, "SENDMESSAGE_URL", &SENDMESSAGE_URL))
  fprintf(logfile,"Sendmessage API URL: %s\n", SENDMESSAGE_URL); else {printf("Manca SENDMESSAGE_URL nella configurazione\n"); exit(0);}

  if (config_lookup_string(&cf, "SUBJECT", &SUBJECT))
  fprintf(logfile,"Alarm message subject: %s\n", SUBJECT); else {printf("Manca SUBJECT nella configurazione\n"); exit(0);}

  if (config_lookup_string(&cf, "MESSAGE", &MESSAGE))
  fprintf(logfile,"Alarm message body: %s\n", MESSAGE); else {printf("Manca MESSAGE nella configurazione\n"); exit(0);}

  if (config_lookup_int(&cf, "TIMEOUT", &TIMEOUT))
  fprintf(logfile,"Timeout (seconds): %d\n", TIMEOUT); else {printf("Manca TIMEOUT nella configurazione\n"); exit(0);}

  if (config_lookup_int(&cf, "PERIOD", &PERIOD))
  fprintf(logfile,"Sample period (seconds): %d\n", PERIOD); else {printf("Manca PERIOD nella configurazione\n"); exit(0);}

  if (config_lookup_string(&cf, "EMAIL_FROM", &EMAIL_FROM))
  fprintf(logfile,"Alarm email_from: %s\n", EMAIL_FROM); else {printf("Manca EMAIL_FROM nella configurazione\n"); exit(0);}

  time_t t = time(NULL);
  struct tm *tm = localtime(&t);
  fprintf(logfile,"\n%s  ", asctime(tm));
  fprintf(logfile,"Starting ...\n");
  fprintf(logfile,"router_alive_check vers. %s\n",version);
  fflush(logfile);
  //initialize MYSQL object for connections
  MYSQL *mysql_conn =  mysql_init(NULL);
  // *** Connect to the database
  if (mysql_real_connect(mysql_conn, DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME, 0, NULL, 0)) {
    fprintf(logfile,"Connesso al database\n");
  } else {
    fprintf(logfile,"Errore di connessione al database\n");
    exit(0);
  }

  int MYSQL_NUM_RES;
  MYSQL_ROW row;

  while(1) {
    sprintf(query, "SELECT router, timestamp from keep_alive_check where alarmed = 0 and timestamp < now() - interval %d second order by timestamp desc limit 1", TIMEOUT);
    fprintf(logfile,".");
    fflush(logfile);
    if (counter == 100) {
      fprintf(logfile,"\n");
      time_t t = time(NULL);
      struct tm *tm = localtime(&t);
      fprintf(logfile,"%s", asctime(tm));
      counter = 0;
    }
    mysql_query(mysql_conn,query);
    MYSQL_RES *result = mysql_store_result(mysql_conn);
    MYSQL_NUM_RES = mysql_num_rows(result);

    if(MYSQL_NUM_RES == 1) {
      row = mysql_fetch_row(result);
      strcpy(router,row[0]);
      strcpy(timestamp,row[1]);
      fprintf(logfile,"\nRilevato router in timeout - timestamp: %s - router: %s\n",timestamp,router);
      fflush(logfile);
      mysql_free_result(result);

      // metti a 1 il flag "alarmed" nella tabella keep_alive_check
      sprintf(query, "UPDATE keep_alive_check set alarmed = 1 where router = '%s'",router);
      fprintf(logfile,"update db: query = %s \n",query);
      mysql_query(mysql_conn,query);

      sprintf(query,"select codUtente from router where router = '%s'",router);
      fprintf(logfile,"query = %s\n",query);
      mysql_query(mysql_conn,query);
      MYSQL_RES *result = mysql_store_result(mysql_conn);
      MYSQL_NUM_RES = mysql_num_rows(result);
      if(MYSQL_NUM_RES == 1) {
        row = mysql_fetch_row(result);
        strcpy(codUtente,row[0]);
        mysql_free_result(result);

        fprintf(logfile,"Notifica a codUtente %s\n",codUtente);
        sprintf(query,"select telegram_flag,telegram_chatid,pushbullett_flag,pushbullett_addr,email_flag,email_addr,whatsapp_flag,whatsapp_tel,sms_flag,sms_tel from notify_method where codUtente = '%s'",codUtente);
        fprintf(logfile,"query = %s\n",query);

        mysql_query(mysql_conn,query);
        MYSQL_RES *result = mysql_store_result(mysql_conn);
        MYSQL_NUM_RES = mysql_num_rows(result);
        if(MYSQL_NUM_RES == 1) {
          while ((row = mysql_fetch_row(result))) {
            strcpy(telegram_flag,row[0]);
            strcpy(telegram_chatid,row[1]);
            strcpy(pushbullett_flag,row[2]);
            strcpy(pushbullett_addr,row[3]);
            strcpy(email_flag,row[4]);
            strcpy(email_addr,row[5]);
            strcpy(whatsapp_flag,row[6]);
            strcpy(whatsapp_tel,row[7]);
            strcpy(sms_flag,row[8]);
            strcpy(sms_tel,row[9]);
          }
          fprintf(logfile,"telegram_flag = %s\n",telegram_flag);
          fprintf(logfile,"telegram_chatid = %s\n",telegram_chatid);
          fprintf(logfile,"pushbullett_flag = %s\n",pushbullett_flag);
          fprintf(logfile,"pushbullett_addr = %s\n",pushbullett_addr);
          fprintf(logfile,"email_flag = %s\n",email_flag);
          fprintf(logfile,"email_addr = %s\n",email_addr);
          fprintf(logfile,"whatsapp_flag = %s\n",whatsapp_flag);
          fprintf(logfile,"whatsapp_tel = %s\n",whatsapp_tel);
          fprintf(logfile,"sms_flag = %s\n",sms_flag);
          fprintf(logfile,"sms_tel = %s\n",sms_tel);
          fflush(logfile);

          if(atoi(telegram_flag) == 1) {
            fprintf(logfile,"Attivato allarme telegram = %s\n",telegram_flag);
            fprintf(logfile," telegram_chatid = %s\n",telegram_chatid);
            sprintf(query,"SELECT telegram_BOT_ID from server_settings");
            fprintf(logfile,"query = %s\n",query);
            mysql_query(mysql_conn,query);
            MYSQL_RES *result = mysql_store_result(mysql_conn);
            MYSQL_NUM_RES = mysql_num_rows(result);
            if(MYSQL_NUM_RES == 1) {
              while ((row = mysql_fetch_row(result))) {
                strcpy(telegram_BOT_ID,row[0]);
                printf("telegram_BOT_ID = %s\n",telegram_BOT_ID);
              }
            }
            mysql_free_result(result);
            sprintf(command,"wget -q -O/dev/null --no-cache --spider \"https://api.telegram.org/%s/sendMessage?chat_id=%s&text=%s - %s\"",telegram_BOT_ID,telegram_chatid,SUBJECT,MESSAGE);
            fprintf(logfile,"%s\n",command);
            fflush(logfile);
            system(command);
          }

          if(atoi(pushbullett_flag) == 1) {
            fprintf(logfile,"Attivato allarme pushbullett = %s\n",pushbullett_flag);
            fprintf(logfile," pushbullett_addr = %s\n",pushbullett_addr);
            sprintf(query,"SELECT pushbullett_token,sendmessage_key from server_settings");
            fprintf(logfile,"query = %s\n",query);
            mysql_query(mysql_conn,query);
            MYSQL_RES *result = mysql_store_result(mysql_conn);
            MYSQL_NUM_RES = mysql_num_rows(result);
            if(MYSQL_NUM_RES == 1) {
              row = mysql_fetch_row(result);
              strcpy(pushbullett_token,row[0]);
              strcpy(sendmessage_key,row[1]);
              printf("pushbullett_token = %s\n",pushbullett_token);
            }
            mysql_free_result(result);
            sprintf(command,"wget --no-cache --spider \"%s?destination=%s&channel=pushbullett&key=%s&subject=%s&message=%s\"",SENDMESSAGE_URL,pushbullett_addr,sendmessage_key,SUBJECT,MESSAGE);
            fprintf(logfile,"%s\n",command);
            fflush(logfile);
            system(command);
          }

          if(atoi(email_flag) == 1) {
            fprintf(logfile,"attivato allarme email = %s con email_addr = %s\n",email_flag,email_addr);
            sprintf(command,"echo \"%s \"|mail -r %s -s \"%s\" %s", MESSAGE, EMAIL_FROM, SUBJECT, email_addr);
            fprintf(logfile,"send mail as: %s \n\r",command);
            fflush(logfile);
            system(command);
          }

          if(atoi(whatsapp_flag) == 1) {
            fprintf(logfile,"attivato allarme whatsapp = %s con whatsapp_tel = %s\n",whatsapp_flag,whatsapp_tel);
          }

          if(atoi(sms_flag) == 1) {
            fprintf(logfile,"Attivato allarme sms = %s\n",sms_flag);
            fprintf(logfile," sms_tel = %s\n",sms_tel);
            sprintf(query,"SELECT sendmessage_key from server_settings");
            fprintf(logfile,"query = %s\n",query);
            mysql_query(mysql_conn,query);
            MYSQL_RES *result = mysql_store_result(mysql_conn);
            MYSQL_NUM_RES = mysql_num_rows(result);
            if(MYSQL_NUM_RES == 1) {
              row = mysql_fetch_row(result);
              strcpy(sendmessage_key,row[0]);
            }
            mysql_free_result(result);
            sprintf(command,"wget --no-cache --spider \"%s?destination=%s&channel=sms&key=%s&subject=%s&message=%s&codUtente=%s\"",SENDMESSAGE_URL,sms_tel,sendmessage_key,SUBJECT,MESSAGE,codUtente);
            fprintf(logfile,"%s\n",command);
            fflush(logfile);
            system(command);
          }
        }
      }
    }
    sleep(PERIOD);
    counter++;
  }
}
