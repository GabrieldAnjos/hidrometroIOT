#include <WiFi.h>
#include <NTPClient.h> //Biblioteca NTPClient modificada
#include <WiFiUdp.h> //Socket UDP
#include <SPI.h>
 
 
//Fuso Horário, no caso horário de verão de Brasília 
int timeZone = -3;
 
//Struct com os dados do dia e hora
struct Date{
    int dayOfWeek;
    int day;
    int month;
    int year;
    int hours;
    int minutes;
    int seconds;
};
 
//Socket UDP que a lib utiliza para recuperar dados sobre o horário
WiFiUDP udp;
 
//Objeto responsável por recuperar dados sobre horário
NTPClient ntpClient(
    udp,                    //socket udp
    "0.br.pool.ntp.org",    //URL do servwer NTP
    timeZone*3600,          //Deslocamento do horário em relacão ao GMT 0
    60000);                 //Intervalo entre verificações online
 
//Nomes dos dias da semana
char* dayOfWeekNames[] = {"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"};
 

void setup()
{
    Serial.begin(115200);
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

  Serial.println("O motivo do despertar foi:  ");
  Serial.println(esp_sleep_get_wakeup_cause());
  
  esp_sleep_enable_ext0_wakeup(GPIO_NUM_4,1); //1 = High, 0 = Low
  
  //esp_sleep_enable_timer_wakeup(TIME_TO_SLEEP * uS_TO_S_FACTOR);
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

void loop()
{
    //Recupera os dados sobre a data e horário
    Date date = getDate();
 
    //Reseta o cursor
 
    //Exibe os dados no display
    Serial.printf(" %s             \n\n %s  \n\n %02d/%02d/%d\n\n %02d:%02d:%02d",
        WiFi.SSID().c_str(),
        dayOfWeekNames[date.dayOfWeek],
        date.day, 
        date.month,
        date.year, 
        date.hours,
        date.minutes, 
        date.seconds);

     Serial.println("Unix time:  ");
    Serial.print(ntpClient.getEpochTime());

   
    
    if(digitalRead(4)==LOW){
      Serial.println("Entrando no modo Deep Sleep...");
      esp_deep_sleep_start();
    }
    delay(100);
}

Date getDate()
{
    //Recupera os dados de data e horário usando o client NTP
    char* strDate = (char*)ntpClient.getFormattedDate().c_str();
 
    //Passa os dados da string para a struct
    Date date;
    sscanf(strDate, "%d-%d-%dT%d:%d:%dZ", 
                    &date.year, 
                    &date.month, 
                    &date.day, 
                    &date.hours, 
                    &date.minutes,
                    &date.seconds);
 
    //Dia da semana de 0 a 6, sendo 0 o domingo
    date.dayOfWeek = ntpClient.getDay();
    return date;
} 
