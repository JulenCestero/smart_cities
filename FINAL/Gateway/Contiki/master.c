/*-------------------------------------*/
#include "contiki.h" 	       	// ContikiOS libraries (should ALWAYS be)
#include "leds.h"    	       	// Leds Driver
#include "dev/serial-line.h"   	// Serial Line Driver
#include "dev/button-sensor.h" 	// User button Driver
#include "dev/light-sensor.h"
#include "dev/sht11-sensor.h"
#include <stdio.h> 	       		// printf(),...
#include "string.h"
#include "dev/uart1.h"	
#include "net/rime.h"
#include <assert.h>
/*-------------------------------------*/
PROCESS(SerialLine_process, "Shell");
PROCESS(LED_process,"LED process");
PROCESS(Broadcast_process,"Broadcast process");
PROCESS(Unicast_process,"Unicast process");
PROCESS(Main_process,"Main process");
AUTOSTART_PROCESSES(&Main_process,&Broadcast_process,&Unicast_process,&SerialLine_process,&LED_process); //

/*-------------------------------------*/

#define SERIAL_BUF_SIZE 128 
static char rx_buf[SERIAL_BUF_SIZE];
static int index_rx_buf;
static struct etimer t1,t2;


static int lig 			=0;
static int temp 		=0;
static int mode_op 		=0;
static int n_led_op 	=0;
static int lig_sl 		=0;
static int temp_sl 		=0;
static int mode_sl 		=0;
static int n_led_sl		=0;
static int mode_sl_s 		=0;
static int n_led_sl_s		=0;
static int n_vecinos 	=0;
static int pos 			=0;
static int pos_s 		=0;
static int cont 		=0;
static int cont_20 		=0;
static int mypos 		=9;

static struct msg_brod
{
	unsigned char commType;  	// Communication type
	int IamMaster;				// if i am master
	unsigned char seqNumber; 	// Sequence Number 
};

static struct msg_uni
{
	unsigned char seqNumber; 	// Sequence Number 
	union
	{
		int light; 					// luminous energy
		unsigned char lightC[2];	
	};
	union
	{
		int temp; 					// luminous energy
		unsigned char tempC[2];	
	};
	union
	{
		int mode; 					// luminous energy
		unsigned char modeC[2];	
	};
	union
	{
		int n_led; 					// luminous energy
		unsigned char n_ledC[2];	
	};
	int ackm;
};

static struct msg_uni_send
{
	unsigned char seqNumber; 	// Sequence Number 
	union
	{
		int mode; 					// luminous energy
		unsigned char modeC[2];	
	};
	union
	{
		int n_led; 					// luminous energy
		unsigned char n_ledC[2];	
	};
};

static struct VecinosList
{
	rimeaddr_t addr;
} Vecinos[8];

static int get_light(void)
{
	//return the luminous energy
	return 10 * light_sensor.value(LIGHT_SENSOR_PHOTOSYNTHETIC) / 7;
}

static int get_temp(void)
{
	//return the temperature (Celsius degrees)
	return ((sht11_sensor.value(SHT11_SENSOR_TEMP) / 10) - 396) / 10;
}

static process_event_t event_serial_data_ready;
static process_event_t event_broadcast;
static process_event_t event_unicast;
static process_event_t event_LED;

static char** str_split(char* a_str, const char a_delim)
{
    char** result    = 0;
    size_t count     = 0;
    char* tmp        = a_str;
    char* last_comma = 0;
    char delim[2];
    delim[0] = a_delim;
    delim[1] = 0;

    /* Count how many elements will be extracted. */
    while (*tmp)
    {
        if (a_delim == *tmp)
        {
            count++;
            last_comma = tmp;
        }
        tmp++;
    }

    /* Add space for trailing token. */
    count += last_comma < (a_str + strlen(a_str) - 1);

    /* Add space for terminating null string so caller
       knows where the list of returned strings ends. */
    count++;

    result = malloc(sizeof(char*) * count);

    if (result)
    {
        size_t idx  = 0;
        char* token = strtok(a_str, delim);

        while (token)
        {
            *(result + idx++) = strdup(token);
            token = strtok(0, delim);
        }
        *(result + idx) = 0;
    }

    return result;
}

static int positionList(rimeaddr_t *address)
{
	int k=0;
	int ret=-1;
	for(k=0;k<=n_vecinos;k++)
	{
		if(rimeaddr_cmp(address,&Vecinos[k].addr)!=0)
		{
			return k;
		}
		else{}
	}
	return ret;
}

static struct broadcast_conn broadcast;

static void receive_brd_function(struct broadcast_conn *c, rimeaddr_t *from)
{
	struct msg_brod *pm = packetbuf_dataptr(); //get Pointer to message
	pos=positionList(from);
	if(pos==-1)
	{	
		Vecinos[n_vecinos].addr.u8[0] 	= from->u8[0];
		n_vecinos++;
		printf("n_vecino_brocast");
	}
	else{}
}

static struct broadcast_callbacks brd_call = {receive_brd_function}; // Callback function declaration

static struct unicast_conn unicast; // Unicast conection declaration

static void receive_uni_function(struct unicast_conn *c, rimeaddr_t *from)
{
	struct msg_uni *pm = packetbuf_dataptr(); //get Pointer to message
	pos=positionList(from);
	if(pos==-1)
	{
		Vecinos[n_vecinos].addr.u8[0] 	= from->u8[0];
		n_vecinos++;
	}
	else
	{
		if(pm->ackm==256)
		{
			//printf("%d.ACK\n",pos);
		}
		else
		{
			lig_sl			= pm -> lightC[1]*255 + pm -> lightC[0];
			temp_sl			= pm -> tempC[1]*255 + pm -> tempC[0];
			mode_sl			= pm -> modeC[1]*255 + pm -> modeC[0];
			n_led_sl		= pm -> n_ledC[1]*255 + pm -> n_ledC[0];
			printf("%d.%d.%d.%d.%d\n",pos,lig_sl,temp_sl,mode_sl,n_led_sl);
		}
	}
}
static struct unicast_callbacks uni_call = {receive_uni_function};

static int uart_rx_callback(unsigned char c) {

     rx_buf[index_rx_buf] = c; 
     if (c == '\n'){
     	index_rx_buf = 0;
	process_post(&SerialLine_process, event_serial_data_ready, rx_buf);

     }else{
     	index_rx_buf++;
     }
}


PROCESS_THREAD(LED_process, ev, data) 
{
	PROCESS_BEGIN(); /*- Init process declaration -*/
	etimer_set(&t2,1*CLOCK_SECOND);
	PROCESS_WAIT_EVENT_UNTIL(ev == event_LED);	
	while(1) 
	{ 
		PROCESS_WAIT_EVENT(); // Wait for events
		if (ev == PROCESS_EVENT_TIMER)
		{
			etimer_reset(&t2);
			if(mode_op==0)
			{
				clock_delay(1000);
				lig=get_light();
				if(lig<150)
				{
					leds_off(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
					leds_on(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
				}
				else if(lig<200 && lig >150)
				{
					leds_off(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
					leds_on(LEDS_RED|LEDS_BLUE);
				}
				else if(lig<300 && lig >200)
				{
					leds_off(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
					leds_on(LEDS_RED);
				}
				else
				{
					leds_off(LEDS_RED|LEDS_BLUE|LEDS_GREEN);				
				}
			}
			else
			{
				if(n_led_op==0)
				{
					leds_off(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
				}
				else if(n_led_op==1)
				{
					leds_off(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
					leds_on(LEDS_RED);
				}
				else if(n_led_op==2)
				{
					leds_off(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
					leds_on(LEDS_RED|LEDS_BLUE);
				}
				else if (n_led_op==3)
				{
					leds_off(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
					leds_on(LEDS_RED|LEDS_BLUE|LEDS_GREEN);
				}
			}
		}
	}
	PROCESS_END(); /*- Finish Process Declaration -*/
}

PROCESS_THREAD(Broadcast_process, ev, data) 
{
	PROCESS_BEGIN(); /*- Init process declaration -*/	
	while(1) 
	{ 
		PROCESS_WAIT_EVENT_UNTIL(ev == event_broadcast);
		cont++; // Increase counter
		struct msg_brod mymsg; // Create a new message
		mymsg.commType	= 1;
		mymsg.IamMaster	= 1;
		mymsg.seqNumber = cont;
		packetbuf_copyfrom(&mymsg,sizeof(struct msg_brod)); // copy message to buffer
		broadcast_send(&broadcast); //Send the message	
	}
	PROCESS_END(); /*- Finish Process Declaration -*/
}

PROCESS_THREAD(Unicast_process, ev, data) 
{
	PROCESS_BEGIN(); /*- Init process declaration -*/;	
	static rimeaddr_t to_addr;
	while(1)
	{
		PROCESS_WAIT_EVENT_UNTIL(ev == event_unicast);	
		int n=0;
		
		cont++; // Increase counter
		static struct msg_uni_send mymsg_uni; // Create a new message
		mymsg_uni.seqNumber = cont;
		mymsg_uni.mode	    = mode_sl_s;
		mymsg_uni.n_led	    = n_led_sl_s;
		packetbuf_copyfrom(&mymsg_uni,sizeof(struct msg_uni)); // copy message to buffer
		to_addr.u8[0] = Vecinos[pos_s].addr.u8[0];
		unicast_send(&unicast,&to_addr.u8[0]); //Send the message
	}
	PROCESS_END(); /*- Finish Process Declaration -*/
}

PROCESS_THREAD(SerialLine_process, ev, data) {

	PROCESS_BEGIN();
		
	// The serial port needs to be initialized, this can be done in the main process or any other as required:
	// String to use with strcmp
	static char * line;	
	static char** tokens;
	static int i=0;
	while(1) 
	{
		PROCESS_WAIT_EVENT_UNTIL(ev == event_serial_data_ready);
		line = (char *) data;
		tokens = str_split(line, '.');
		if (tokens)
		    {	
		    	if(strncmp(*(tokens + i), "9", strlen("9")) == 0)
			{
				free(*(tokens + i));
				i++;
				mode_op = atoi(*(tokens + i));
				free(*(tokens + i));
				i++;
				n_led_op = atoi(*(tokens + i));	
				free(*(tokens + i));
			}
			else
			{
				pos_s=atoi(*(tokens + i));
				free(*(tokens + i));
				i++;
				mode_sl_s=atoi(*(tokens + i));	
				free(*(tokens + i));
				i++;
				n_led_sl_s=atoi(*(tokens + i));	
				free(*(tokens + i));
				process_post(&Unicast_process, event_unicast,"");
			}
			i=0;
			free(tokens);
		    }
	}
	PROCESS_END();
}

PROCESS_THREAD(Main_process, ev, data) {
	PROCESS_BEGIN(); /*- Init process declaration -*/
	uart1_init(BAUD2UBR(115200)); //set the baud rate as necessary
	uart1_set_input(uart_rx_callback); //set the callback function

	SENSORS_ACTIVATE(light_sensor);
	SENSORS_ACTIVATE(sht11_sensor);
	
    	// Allocate the required event
    	event_serial_data_ready 	= process_alloc_event();
	event_broadcast 		= process_alloc_event();
	event_unicast 			= process_alloc_event();
	event_LED			= process_alloc_event();

	broadcast_open(&broadcast, 129, &brd_call);
	unicast_open(&unicast, 130, &uni_call);
	etimer_set(&t1,5*CLOCK_SECOND);
	process_post(&Broadcast_process, event_broadcast,"");
	process_post(&LED_process, event_LED,"");
	while(1) 
	{ 
		PROCESS_WAIT_EVENT(); // Wait for events
		if (ev == PROCESS_EVENT_TIMER)
		{ // Check the event type
			cont_20++;
			etimer_reset(&t1); // Reset timer
			if(cont_20==5)
			{
				process_post(&Broadcast_process, event_broadcast,"");
				lig=get_light();
				temp=get_temp();
				printf("%d.%d.%d.%d.%d\n",mypos,lig,temp,mode_op,n_led_op);
				cont_20=0;
			}
			else
			{
				lig=get_light();
				temp=get_temp();
				printf("%d.%d.%d.%d.%d\n",mypos,lig,temp,mode_op,n_led_op);
				
			}
		}
	}
	PROCESS_END(); /*- Finish Process Declaration -*/
}



















