// uso: ihex 0001
// prepara il file in formato intelHex per la progammazione del seriale



#include <stdio.h>
#include <string.h>
// https://www.fischl.de/hex_checksum_calculator/?

int main(int argc, char* argv[]) {
FILE *fd;
 fd=fopen(argv[1], "w");
  if( fd==NULL ) {
    exit(1);
  }
    int i, len, sum;
    printf("argv[1]:=%s\n",argv[1]);
    char word[17];
    strcpy(word,argv[1]);
    char outword[33];
    char prefix[10] = ":04000300";
    char checksum;
//    printf("Intro word:");
//    fgets(word, sizeof(word), stdin);
    len = strlen(word);
    if(word[len-1]=='\n')
        word[--len] = '\0';

    for(i = 0; i<len; i++){
        sprintf(outword+i*2, "%02X", word[i]);
    }
sum = 7; //prefix sum
for(i=0; i<len; i++){
sum = sum + word[i];
}

// printf("Hex word = %s\n", outword);
// printf("sum = %02X\n",sum);
checksum = 255 - sum + 1;
fprintf(fd,"%s%s%02X\n:00000001FF\n", prefix,outword,checksum);
  fclose(fd);
    return 0;
}
