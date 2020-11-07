package server

import (
	"net"
)

type impl struct {
	c			*net.TCPConn
}

func newImpl(c *net.TCPConn) *impl {
	return &impl{c}
}

func (c *impl) Read() []byte {

}

func (c *impl) Write([]byte) {

}