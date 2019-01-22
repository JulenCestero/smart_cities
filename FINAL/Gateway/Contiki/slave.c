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
/*-------------------------------------*/

PROCESS(LED_process,"LED process");
PROCESS(Broadcast_process,"Broadcast process");
PROCESS(Unicast_process,"Unicast process");
PROCESS(Main_process,"Main process");
AUTOSTART_PROCESSES(&LED_process,&Broadcast_process,&Unicast_process,&Main_process);

/*-------------------------------------*/

#define SERIAL_BUF_SIZE 128 
static char rx_buf[SERIAL_BUF_SIZE];
static int index_rx_buf;
static struct etimer t1,t2;


static int lig 			=0;
static int temp 		=0;
static int mode_op 		=0;
static int n_led_op 	=0;
static int n_vecinos 	=0;
static int pos 			=0;
static int cont 		=0;
static int cont_20 		=0;
static int ACK			=0;
static int pos_master 	=-1;

static struct msg_brod
{
	unsigned char commType;  	// Communication type
	int IamMaster;				// if i am master
	unsigned char seqNumber; 	// Sequence Number 
};
static struct msg_uni_send
{
	unsigned char seqNumber; 	// Sequence Number 
	int light; 					// luminous energy
	int temp; 					// temperature ºC
	int mode;
	int n_led;
	int ackm;
};

static struct msg_uni_receive
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
	int Master;
} Vecinos[8];
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

static process_event_t event_broadcast;
static process_event_t event_unicast;
static process_event_t event_LED;

static struct broadcast_conn broadcast;

static void receive_brd_function(struct broadcast_conn *c, rimeaddr_t *from)
{
	struct msg_brod *pm = packetbuf_dataptr(); //get Pointer to message
	pos=positionList(from);
	if(pos==-1)
	{	
		Vecinos[n_vecinos].addr.u8[0] 	= from->u8[0];
		Vecinos[n_vecinos].Master 	= pm->IamMaster;
		printf("New vecino %\n",pm->IamMaster);
		if(pm->IamMaster==256)
		{
			pos_master = n_vecinos;
			printf("new master\n");
		}
		n_vecinos++;
	}
	else{}
}

static struct broadcast_callbacks brd_call = {receive_brd_function}; // Callback function declaration

static struct unicast_conn unicast; // Unicast conection declaration

static void receive_uni_function(struct unicast_conn *c, rimeaddr_t *from)
{
	struct msg_uni_receive *pm 	= packetbuf_dataptr(); //get Pointer to message
	if(pos_master!=-1)
	{
		if(rimeaddr_cmp(from,&Vecinos[pos_master].addr)!=0)	
		{
			mode_op						= pm -> modeC[1]*255 + pm -> modeC[0];
			n_led_op					= pm -> n_ledC[1]*255 + pm -> n_ledC[0];
			ACK 						= 1;
			printf("uni Master: %d,%d\n",mode_op,n_led_op);
		}
	}
	//process_post(&Unicast_process, event_unicast,"");
}
static struct unicast_callbacks uni_call = {receive_uni_function};


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
				printf("Modo: %d,%d,%d",mode_op,n_led_op, lig);
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
		mymsg.IamMaster	= 0;
		mymsg.seqNumber = cont;
		packetbuf_copyfrom(&mymsg,sizeof(struct msg_brod)); // copy message to buffer
		broadcast_send(&broadcast); //Send the message
	}
	PROCESS_END(); /*- Finish Process Declaration -*/
}

PROCESS_THREAD(Unicast_process, ev, data) 
{
	PROCESS_BEGIN(); /*- Init process declaration -*/
	static rimeaddr_t to_addr;
	while(1)
	{
		PROCESS_WAIT_EVENT_UNTIL(ev == event_unicast);	
		int n=0;
		if(n_vecinos==0){}
		else
		{
			static struct msg_uni_send mymsg_uni; // Create a new message	
			if(ACK==0)
			{
				cont++; // Increase counter
				mymsg_uni.seqNumber = cont;
				mymsg_uni.light	    = get_light();
				mymsg_uni.temp	    = get_temp();
				mymsg_uni.mode	    = mode_op;
				mymsg_uni.n_led	    = n_led_op;
				mymsg_uni.ackm 		= 0;
				
			}
			else
			{
				mymsg_uni.seqNumber = cont;
				mymsg_uni.light	    = 0;
				mymsg_uni.temp	    = 0;
				mymsg_uni.ackm 		= 1;
				ACK=0;
			}
			packetbuf_copyfrom(&mymsg_uni,sizeof(struct msg_uni_send)); // copy message to buffer
			to_addr.u8[0] = Vecinos[pos_master].addr.u8[0];
			printf("Send unicast %d, %d,%d,%d,%d\n",mymsg_uni.seqNumber,mymsg_uni.light,mymsg_uni.temp,mymsg_uni.mode,mymsg_uni.n_led);
			unicast_send(&unicast,&to_addr.u8[0]); //Send the message
		}
	}
	PROCESS_END(); /*- Finish Process Declaration -*/
}

PROCESS_THREAD(Main_process, ev, data) {
	PROCESS_BEGIN(); /*- Init process declaration -*/	
	broadcast_open(&broadcast, 129, &brd_call);
	unicast_open(&unicast, 130, &uni_call);
	etimer_set(&t1,5*CLOCK_SECOND);
	SENSORS_ACTIVATE(light_sensor);
	SENSORS_ACTIVATE(sht11_sensor);

	event_broadcast 		= process_alloc_event();
	event_unicast 			= process_alloc_event();
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
				process_post(&Unicast_process, event_unicast,"");
				cont_20=0;
			}
			else
			{
				process_post(&Unicast_process, event_unicast,"");
			}
		}
	}
	PROCESS_END(); /*- Finish Process Declaration -*/
}

