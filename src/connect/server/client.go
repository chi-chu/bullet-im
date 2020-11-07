package server

import (
	"github.com/chi-chu/bullet-im/src/connect/message"
	"github.com/chi-chu/log"
	"sync/atomic"
	"time"
)

type Conn interface {
	Read() []byte
	Write([]byte)
}

type Client struct {
	c				Conn
	uid				int
	device			string
	msgChan			chan *message.Message
	login			bool
	stop			chan struct{}

}

func newClient(c Conn) *Client {
	o := Client{
		c: 			c,
		msgChan: 	make(chan *message.Message, server.c.MsgCacheSize),
		stop:		make(chan struct{}),
	}
	return &o
}

func (c *Client) Run() {
	atomic.AddUint64(&server.status.ConnectCount, 1)
	go c.Receive()
	go c.Send()
}

func (c *Client) Receive() {
	for {
		data := c.c.Read()
	}
}

func (c *Client) Send() {
	for {
		select {
		case msg := <- c.msgChan:
			log.Debug("send msg %+v", msg)
			data := server.p.EncodeMessage(msg)
			c.c.Write(data)
		case <-time.After(time.Duration(server.c.HeartBeatTime)*time.Second):
			log.Debug("send ping msg")
			c.c.Write(server.p.EncodeMessage(message.NewPingMsg()))
		case <-c.stop:
			log.Debug("client %d device %s exit", c.uid, c.device)
			return
		}
	}
}