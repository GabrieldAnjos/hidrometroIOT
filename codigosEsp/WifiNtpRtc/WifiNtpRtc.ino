#include <WiFi.h>
#include <NTPClient.h> //Biblioteca NTPClient modificada
#include <WiFiUdp.h> //Socket UDP
#include <SPI.h>
#include <freertos/FreeRTOS.h>
#include <freertos/task.h>
#include <esp_system.h>
#include <time.h>
#include <sys/time.h>
//#include "driver/adc.h"
//#include <esp_wifi.h>
//#include <esp_bt.h>

#define uS_TO_S_FACTOR 1000000
#define DESPERTAR 1 //Hora que vai Despertar
#define DORMIR 1 //Minutos de inativadade para dormir

//tempo que o ESP32 ficará em modo sleep (em segundos)

uint64_t TIME_TO_SLEEP = 15;

const char* host = "192.168.0.107";
const uint16_t port = 80;

WiFiClient client;

WiFiUDP udp;

int timeZone = -3;
NTPClient ntpClient(
  udp,                    //socket udp
  "0.br.pool.ntp.org",    //URL do servwer NTP
  timeZone * 3600,        //Deslocamento do horário em relacão ao GMT 0
  60000);

struct tm data;//Cria a estrutura que contem as informacoes da data.

RTC_DATA_ATTR int consumoPulso = 2;

String id_hidrometro = "2" ;
int ultimoMin = 0;

void setup()
{
  Serial.begin(115200);

  if (esp_sleep_get_wakeup_cause() == 0 || esp_sleep_get_wakeup_cause() == 4) {
    connectWiFi();
    setupNTP();
    connectServidor();

    timeval tv;//Cria a estrutura temporaria para funcao abaixo.

    tv.tv_sec =  ntpClient.getEpochTime();//Atribui minha data atual. Voce pode usar o NTP para isso ou o site citado no artigo!
    settimeofday(&tv, NULL);//Configura o RTC para manter a data atribuida atualizada.
    consumoPulso = 2;
  }

  WiFi.disconnect(true);
  WiFi.mode(WIFI_OFF);
  btStop();

  //adc_power_off();
  //esp_wifi_stop();
  //esp_bt_controller_disable();
}

void setupNTP()
{
  //Inicializa o client NTP
  ntpClient.begin();

  //Espera pelo primeiro update online
  Serial.println("Esperando atualizar Hora");
  while (!ntpClient.update())
  {
    Serial.print(".");
    ntpClient.forceUpdate();
    delay(500);
  }

  Serial.println();
  Serial.println("Hora Atualizada");
}



void connectWiFi()
{
  Serial.println("Conectando ao WIFI");

  //Troque pelo nome e senha da sua rede WiFi
  WiFi.begin("Cafézim", "topcafezim");

  //Espera enquanto não estiver conectado
  while (WiFi.status() != WL_CONNECTED)
  {
    Serial.print(".");
    delay(500);
  }

  Serial.println();
  Serial.print("Connectado a ");
  Serial.println(WiFi.SSID());
}

void connectServidor() {
  Serial.print("Conectando ao Servidor ");
  Serial.print(host);
  Serial.print(':');
  Serial.println(port);

  // Use WiFiClient class to create TCP connections

  // WiFiClient client;
  if (!client.connect(host, port)) {
    Serial.println("connection failed");
    delay(5000);
    return;
  }

  String url = "/TCC/salvar.php?";
  url += "consumoPulso=";
  url += consumoPulso;
  url += "&id_hidrometro=";
  url += id_hidrometro;

  Serial.print("Requesitando URL: ");
  Serial.println(url);

  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");

  // wait for data to be available
  unsigned long timeout = millis();
  while (client.available() == 0) {
    if (millis() - timeout > 5000) {
      Serial.println(">>> Client Timeout !");
      client.stop();
      delay(60000);
      return;
    }
  }
  // Close the connection
  Serial.println();
  Serial.println("Fechando Conexão");
  client.stop();
}

void loop() {
  vTaskDelay(pdMS_TO_TICKS(1000));//Espera 1 seg
  Serial.println("Motivo acordar:");
  print_wakeup_reason();

  time_t tt = time(NULL);//Obtem o tempo atual em segundos. Utilize isso sempre que precisar obter o tempo atual
  data = *gmtime(&tt);//Converte o tempo atual e atribui na estrutura

  char data_formatada[64];
  strftime(data_formatada, 64, "%d/%m/%Y %H:%M:%S", &data);//Cria uma String formatada da estrutura "data"
  int hora = data.tm_hour;
  int minutos = data.tm_min;
  Serial.printf("\nUnix Time: %d\n", int32_t(tt));//Mostra na Serial o Unix time
  Serial.printf("Data formatada: %s\n", data_formatada);//Mostra na Serial a data formatada
  Serial.printf("AS horas sao: %d",  hora);//Mostra na Serial a data formatada

  if (digitalRead(4) == LOW) {

    if (ultimoMin == 0) {
      ultimoMin = minutos;
      if(ultimoMin == 59){
        ultimoMin = 0;
      }
    }
    if (ultimoMin + DORMIR == minutos) { //vai desligar após 3 minutos de inatividade
      ultimoMin = 0;

      if (DESPERTAR > hora) {
        TIME_TO_SLEEP = (DESPERTAR - hora) * 3600 - minutos * 60; //Tempo para despertar é igual ao tempo em segundos para
      }
      else if (DESPERTAR < hora) {
        TIME_TO_SLEEP = (DESPERTAR + 24 - hora) * 3600 - minutos * 60;
      }
      else {
        TIME_TO_SLEEP = 24 * 3600 - minutos * 60;
      }
      Serial.println("Faltam horas para a 8");
      Serial.printf("Faltam : %d",  TIME_TO_SLEEP);//Mostra na Serial a data formatada
       

      esp_sleep_enable_timer_wakeup(TIME_TO_SLEEP * uS_TO_S_FACTOR);

      esp_sleep_enable_ext0_wakeup(GPIO_NUM_4, 1); //1 = High, 0 = Low

      Serial.println("Entrando no modo Sleep");
      esp_deep_sleep_start();

    }
  }
  else {
    consumoPulso = consumoPulso + 2;
    Serial.printf("Número de Pulsos: %d", consumoPulso);
  }
  Serial.println("Motivo acordar");
  Serial.println(esp_sleep_get_wakeup_cause());
  print_wakeup_reason();
}
void print_wakeup_reason( ) {
  esp_sleep_wakeup_cause_t wakeup_reason;
  String reason = "";

  wakeup_reason = esp_sleep_get_wakeup_cause(); //recupera a causa do despertar

  switch (wakeup_reason)
  {
    case 1 : reason = "EXT0 RTC_IO BTN"; break;
    case 2 : reason = "EXT1 RTC_CNTL";   break;
    case 3 : reason = "TIMER";           break;
    case 4 : reason = "TOUCHPAD";        break;
    case 5 : reason = "ULP PROGRAM";     break;
    default : reason = "NO DS CAUSE";    break;
  }
  Serial.println(reason);

}
