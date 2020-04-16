/*
    This sketch establishes a TCP connection to a "quote of the day" service.
    It sends a "hello" message, and then prints received data.
*/

#include <WiFi.h>

#ifndef STASSID
#define STASSID "Cafézim"
#define STAPSK  "topcafezim"
#endif

#define uS_TO_S_FACTOR 1000000
//tempo que o ESP32 ficará em modo sleep (em segundos)
#define TIME_TO_SLEEP 10

const char* ssid     = STASSID;
const char* password = STAPSK;

const char* host = "192.168.0.107";
const uint16_t port = 80;

String id_hidrometro = "2" ;
int consumoPulso = 2 ;


void setup() {
  Serial.begin(9600);
  delay(10);
  // We start by connecting to a WiFi network

  
  

  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  /* Explicitly set the ESP8266 to be a WiFi-client, otherwise, it by default,
     would try to act as both a client and an access-point and could cause
     network-issues with your other WiFi-devices on your WiFi-network. */
  //WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());

  Serial.println("O motivo do despertar foi:  ");
  Serial.println(esp_sleep_get_wakeup_cause());
  
    esp_sleep_enable_ext0_wakeup(GPIO_NUM_4,1); //1 = High, 0 = Low
  
  esp_sleep_enable_timer_wakeup(TIME_TO_SLEEP * uS_TO_S_FACTOR);
}

void loop() {

  
    Serial.println(consumoPulso);

    
    if(digitalRead(4)==HIGH){
       consumoPulso = consumoPulso + 2;
    }
   // WiFi.mode (WIFI_OFF);
    //btStop ();
  
  consumoPulso = consumoPulso + 1;
  Serial.print("connecting to ");
  Serial.print(host);
  Serial.print(':');
  Serial.println(port);

  // Use WiFiClient class to create TCP connections
  WiFiClient client;
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

  Serial.print("Requesting URL: ");
  Serial.println(url);

   client.print(String("GET ") + url + " HTTP/1.1\r\n" + 
                "Host: " + host + "\r\n" +
                "Connection: close\r\n\r\n");
  
  /* This will send a string to the server
  Serial.println("sending data to server");
  if (client.connected()) {
    client.println("hello from ESP8266");
  }
  */
  
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

//   Read all the lines of the reply from server and print them to Serial
  

  // Close the connection
  Serial.println();
  Serial.println("closing connection");
  client.stop();


  
 
   
  delay(1000); // execute once every 5 minutes, don't flood remote service
}
