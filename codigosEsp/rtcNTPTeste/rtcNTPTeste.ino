#include <WiFi.h>
#include <NTPClient.h> //Biblioteca NTPClient modificada
#include <WiFiUdp.h> //Socket UDP
#include <SPI.h>
#include <freertos/FreeRTOS.h>
#include <freertos/task.h>
#include <esp_system.h>
#include <time.h>
#include <sys/time.h>
struct tm data;//Cria a estrutura que contem as informacoes da data.

int timeZone = -3;

WiFiUDP udp;

NTPClient ntpClient(
    udp,                    //socket udp
    "0.br.pool.ntp.org",    //URL do servwer NTP
    timeZone*3600,          //Deslocamento do horário em relacão ao GMT 0
    60000);   

RTC_DATA_ATTR int modoSleep = 0;

void setup()
{
  Serial.begin(115200);
  
  if(modoSleep == 0){
    connectWiFi();
    setupNTP();
    
    //Cria uma nova tarefa no core 0
    xTaskCreatePinnedToCore(
    wifiConnectionTask,     //Função que será executada
    "wifiConnectionTask",   //Nome da tarefa
    10000,                  //Tamanho da memória disponível (em WORDs)
    NULL,                   //Não vamos passar nenhum parametro
    2,                      //prioridade
    NULL,                   //Não precisamos de referência para a tarefa
    0);                     //Número do core

    timeval tv;//Cria a estrutura temporaria para funcao abaixo.
    
    tv.tv_sec =  ntpClient.getEpochTime();//Atribui minha data atual. Voce pode usar o NTP para isso ou o site citado no artigo!
    settimeofday(&tv, NULL);//Configura o RTC para manter a data atribuida atualizada.
  }
 
  Serial.println("O motivo do despertar foi:  ");
  Serial.println(esp_sleep_get_wakeup_cause());
  
  esp_sleep_enable_ext0_wakeup(GPIO_NUM_4,1); //1 = High, 0 = Low
}

void setupNTP()
{
    //Inicializa o client NTP
    ntpClient.begin();
     
    //Espera pelo primeiro update online
    Serial.println("Waiting for first update");
    while(!ntpClient.update())
    {
        Serial.print(".");
        ntpClient.forceUpdate();
        delay(500);
    }
 
    Serial.println();
    Serial.println("First Update Complete");
}

void wifiConnectionTask(void* param)
{
    while(true)
    {
        //Se a WiFi não está conectada
        if(WiFi.status() != WL_CONNECTED)
        {
            //Manda conectar
            connectWiFi();
        }
 
        //Delay de 100 ticks
        vTaskDelay(100);
    }
}

void connectWiFi()
{
    Serial.println("Connecting");
 
    //Troque pelo nome e senha da sua rede WiFi
    WiFi.begin("Cafézim", "topcafezim");
     
    //Espera enquanto não estiver conectado
    while(WiFi.status() != WL_CONNECTED)
    {
        Serial.print(".");
        delay(500);
    }
 
    Serial.println();
    Serial.print("Connected to ");
    Serial.println(WiFi.SSID());
}

void loop(){
    vTaskDelay(pdMS_TO_TICKS(1000));//Espera 1 seg
    time_t tt = time(NULL);//Obtem o tempo atual em segundos. Utilize isso sempre que precisar obter o tempo atual
    data = *gmtime(&tt);//Converte o tempo atual e atribui na estrutura
    
    char data_formatada[64];
    int hora;
    strftime(data_formatada, 64, "%d/%m/%Y %H:%M:%S", &data);//Cria uma String formatada da estrutura "data"
    hora = data.tm_hour;
    Serial.printf("\nUnix Time: %d\n", int32_t(tt));//Mostra na Serial o Unix time
    Serial.printf("Data formatada: %s\n", data_formatada);//Mostra na Serial a data formatada
    Serial.printf("AS horas sao: %d",  hora);//Mostra na Serial a data formatada
      /*
        Com o Unix time, podemos facilmente controlar acoes do MCU por data, visto que utilizaremos os segundos
        e sao faceis de usar em IFs
        Voce pode criar uma estrutura com a data desejada e depois converter para segundos (inverso do que foi feito acima)
        caso deseje trabalhar para atuar em certas datas e horarios
        No exemplo abaixo, o MCU ira printar o texto **APENAS** na data e horario (28/02/2019 12:00:05) ate (28/02/2019 12:00:07)
      */
    if (tt >= 1551355205 && tt < 1551355208)//Use sua data atual, em segundos, para testar o acionamento por datas e horarios
    {
      Serial.println("Acionando carga durante 3 segundos...\n");
    }
    
    if(digitalRead(4)==LOW){
      Serial.println("Entrando no modo Deep Sleep...");
      modoSleep = 1;
      esp_deep_sleep_start();
    }
 }
