import socket
import string
import sys
import re
from threading import Thread

feed_network = "irc.feedhost.tld"
feed_network_port = 6667 #or whatever the port is

report_network = "irc.reporthost.tld"
report_network_port = 6667 #or whatever the port is

nick = "nick"

nickserv_user = "nickserv user"
nickserv_pass = "nickserv password"

report_channel = "#report"
feed_channel = "#feed"

ircsock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
feedsock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

running = 1

regex = re.compile("\x1f|\x02|\x12|\x0f|\x16|\x03(?:\d{1,2}(?:,\d{1,2})?)?", re.UNICODE) # http://stackoverflow.com/questions/970545/how-to-strip-color-codes-used-by-mirc-users/970723

def ping(network): # Replies to PINGs from the server
	network.send("PONG :Pong\n")
	
def sendmsg(network,chan, msg): #Sends messages to channel
	network.send("PRIVMSG " + chan + " :" + msg + "\n")
	
def joinchan(network,chan): #Joins channels
	network.send("JOIN " + chan + "\n")
	
def reporting_network():
	global running
	ircsock.connect((report_network, report_network_port))
	ircsock.send("USER "+ nick +" "+ nick +" "+ nick +" :I exist.\n") # user authentication
	ircsock.send("NICK "+ nick +"\n") # here we actually assign the nick to the bot

	sendmsg(ircsock,"NickServ", "IDENTIFY " + nickserv_user + " " + nickserv_pass)

	
	while running:
		ircmsg = ircsock.recv(2048) #recieves message
		ircmsg = ircmsg.strip('\n\r') #removes unneeded new lines
		
		irc_stuff = ircmsg.split(' ')
		
		if ircmsg.find(' 396 ') != -1:
			joinchan(ircsock, report_channel)

		if ircmsg.find(' PRIVMSG ')!=-1:
			irc_stuff=ircmsg.split(' ')

		print(ircmsg) #print what happens
		
		if ircmsg.find("PING :") != -1: #If it pings us
			ping(ircsock)
			
		if ircmsg.find(":~halp") != -1:
			sendmsg(ircsock, irc_stuff[2], "I report edits to pages. Pages I report on can be found at <link>| ~explode kills me")
		
		if (ircmsg.find(":~explode") != -1) and ("@yourhost" in irc_stuff[0]): # Place your cloak in place of @yourhost
			running = 0
			
		del irc_stuff
	
Thread(target=reporting_network).start()

# stuff for feed network
feedsock.connect((feed_network, feed_network_port))

feedsock.send("USER " + nick + " " + nick + " " + nick + " :I exist.\n")
feedsock.send("NICK " + nick + "\n")

joinchan(feedsock,feed_channel)

while running:

	feedmsg = feedsock.recv(2048)
	feedmsg = feedmsg.strip('\n\r')
	
	feedmsg = regex.sub("", feedmsg)
	
	print(feedmsg)
	
	if feedmsg.find("wiki url") != -1: #written for use on Wikia, so feed included many wikis
		start = int(feedmsg.find('[['))
		end = int(feedmsg.find(']]'))
		pagename = feedmsg[start:end + 2]	
		
		start = int(feedmsg.find('wiki url'))
		end = int(feedmsg.find(' ', start))
		diff_url = feedmsg[start:end]
		
		start = int(feedmsg.find('*')) + 1
		end = int(feedmsg.find('*', start + 1))
		user = feedmsg[start:end]
		
		summary = feedmsg[end + 2:-1]
		
		#TODO: make more neat. Follow syntax below currently
		
		if pagename.find("Forum:") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Counter-Vandalism Unit") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Administrator requests") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:User help") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Requests for adminship/") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:AutoWikiBrowser/Requests") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Requests for permissions") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Clan Chat/Requests for CC Rank") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Requests for chat moderator/") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Requests for splitting/") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Requests for deletion/") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Requests for undeleting/") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Requests for merging/") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		elif pagename.find("RuneScape:Signatures/Requests") != -1:
			sendmsg(ircsock,report_channel, pagename + " was edited by" + user + "| " + diff_url + " | " + summary)
		
	if feedmsg.find("PING :") != -1:
		ping(feedsock)