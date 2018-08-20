// g++ `mysql_config --cflags` `mysql_config --libs` -o ppp ppp.cpp
#include <cstdlib>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <mysql/mysql.h>

#define DATABASE_SERVER "localhost"
#define DATABASE_NAME "hooly"
#define DATABASE_USERNAME "hooly"
#define DATABASE_PASSWORD "hooly_pwd"

int main(void)
{
  //initialize MYSQL object for connections
  MYSQL *mysql_conn =  mysql_init(NULL);
  // *** Connect to the database
  if (mysql_real_connect(mysql_conn, DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME, 0, NULL, 0)) {
    fprintf(stdout,"Connesso al database\n");
  } else {
    fprintf(stdout,"Errore di connessione al database\n");
    exit(0);
  }

  char query[1024];
  char subquery[512];
  char command[512];
  char subject[64] = "Allarme da Hooly router ";
  char message [64] = "Non ricevo dati da oltre 2 minuti";
  char* router[16];
  char* timestamp[64];
  char* codUtente;
  char* telegram_flag;
  char* telegram_chatid;
  char* pushbullett_flag;
  char* pushbullett_addr;
  char* email_flag;
  char* email_addr;
  char* whatsapp_flag;
  char* whatsapp_tel;
  char* pushbullett_token;
  char* telegram_BOT_ID;
  int counter = 0;

  int MYSQL_NUM_RES;
  MYSQL_ROW row;

  while(1) {
    sprintf(query, "SELECT router, timestamp from keep_alive_check where alarmed = 0 and timestamp < now() - interval 150 second");
    fprintf(stdout,".");
    if (counter == 50) {
      fprintf(stdout,"\n");
      counter = 0;
    }
    fflush(stdout);
    mysql_query(mysql_conn,query);
    MYSQL_RES *result = mysql_store_result(mysql_conn);
    MYSQL_NUM_RES = mysql_num_rows(result);

    if(MYSQL_NUM_RES > 0) {
      fprintf(stdout,"\nRilevato router in timeout\n");
      int x = 0;  // x = vettore numero di router, potrebbero essercene diversi
      while ((row = mysql_fetch_row(result))) {
        router[x] = row[0];
        timestamp[x] = row[1];
        fprintf(stdout,"timestamp: %s - router: %s\n",timestamp[x],router[x]);
        x++;
      }
      mysql_free_result(result);


      for (int a=0; a<x; a++) { // per ciascun router x, a = altro vettore al router
        // metti a 1 il flag "alarmed" nella tabella keep_alive_check
        sprintf(query, "UPDATE keep_alive_check set alarmed = 1 where router = '%s'",router[a]);
        fprintf(stdout,"update db: query = %s \n",query);
        mysql_query(mysql_conn,query);

        sprintf(query,"select codUtente from router where router = '%s'",router[a]);
        fprintf(stdout,"query = %s\n",query);
        mysql_query(mysql_conn,query);
        MYSQL_RES *result = mysql_store_result(mysql_conn);
        MYSQL_NUM_RES = mysql_num_rows(result);
        if(MYSQL_NUM_RES = 1) {
          while ((row = mysql_fetch_row(result))) {
            codUtente = row[0];
          }
          mysql_free_result(result);

          fprintf(stdout,"Notifica a codUtente %s\n",codUtente);
          sprintf(query,"select telegram_flag,telegram_chatid,pushbullett_flag,pushbullett_addr,email_flag,email_addr,whatsapp_flag,whatsapp_tel from notify_method where codUtente = '%s'",codUtente);
          fprintf(stdout,"query = %s\n",query);

          mysql_query(mysql_conn,query);
          MYSQL_RES *result = mysql_store_result(mysql_conn);
          MYSQL_NUM_RES = mysql_num_rows(result);
          if(MYSQL_NUM_RES = 1) {
            while ((row = mysql_fetch_row(result))) {
              telegram_flag = row[0];
              telegram_chatid = row[1];
              pushbullett_flag = row[2];
              pushbullett_addr = row[3];
              email_flag = row[4];
              email_addr = row[5];
              whatsapp_flag = row[6];
              whatsapp_tel = row[7];
            }
            fprintf(stdout,"telegram_flag = %s\n",telegram_flag);
            fprintf(stdout,"telegram_chatid = %s\n",telegram_chatid);
            fprintf(stdout,"pushbullett_flag = %s\n",pushbullett_flag);
            fprintf(stdout,"pushbullett_addr = %s\n",pushbullett_addr);
            fprintf(stdout,"email_flag = %s\n",email_flag);
            fprintf(stdout,"email_addr = %s\n",email_addr);
            fprintf(stdout,"whatsapp_flag = %s\n",whatsapp_flag);
            fprintf(stdout,"whatsapp_tel = %s\n",whatsapp_tel);


            if(atoi(telegram_flag) == 1) {
              sprintf(query,"SELECT telegram_BOT_ID from server_settings");
              fprintf(stdout,"query = %s\n",query);
              mysql_query(mysql_conn,query);
              MYSQL_RES *result = mysql_store_result(mysql_conn);
              MYSQL_NUM_RES = mysql_num_rows(result);
              if(MYSQL_NUM_RES = 1) {
                while ((row = mysql_fetch_row(result))) {
                  telegram_BOT_ID = row[0];
                  printf("telegram_BOT_ID = %s\n",telegram_BOT_ID);
                }
              }
              mysql_free_result(result);
              sprintf(command,"wget -q -O/dev/null --no-cache --spider \"https://api.telegram.org/%s/sendMessage?chat_id=%s&text=%s-%s\"",telegram_BOT_ID,telegram_chatid,subject,message);              fprintf(stdout,"Comando: %s \n\r",command);
              system(command);
            }

            if(atoi(pushbullett_flag) == 1) {
              sprintf(query,"SELECT pushbullett_token from server_settings");
              fprintf(stdout,"query = %s\n",query);
              mysql_query(mysql_conn,query);
              MYSQL_RES *result = mysql_store_result(mysql_conn);
              MYSQL_NUM_RES = mysql_num_rows(result);
              if(MYSQL_NUM_RES = 1) {
                while ((row = mysql_fetch_row(result))) {
                  pushbullett_token = row[0];
                  printf("pushbullett_token = %s\n",pushbullett_token);
                }
              }
              mysql_free_result(result);
              sprintf(command,"wget --no-cache --spider \"http://myhooly.hooly.eu/sendmessage.php?destination=%s&channel=pushbullett&key=2479094823fhjkIacopo&subject=%s&message=%s\"",pushbullett_addr,subject,message);
              fprintf(stdout,"Comando: %s \n\r",command);
              system(command);
            }

            if(atoi(email_flag) == 1) {
              fprintf(stdout,"Inviato allarme email = %s con email_addr = %s\n",email_flag,email_addr);
              sprintf(command,"echo \"Allarme da router %s; non ricevo dati da 2 min.\"|mail -r hooly@hooly.eu -s \"Allarme di collegamento router %s\" %s",router[x],router[x],email_addr);
              fprintf(stdout,"Comando: %s \n\r",command);
              system(command);
            }

            if(atoi(whatsapp_flag) == 1) {
              fprintf(stdout,"attivato allarme whatsapp = %s con whatsapp_tel = %s\n",whatsapp_flag,whatsapp_tel);
              // inserire notifica whatsapp
            }
          }
        }
      }
    }
    sleep(15);
    counter++;
  }
}
