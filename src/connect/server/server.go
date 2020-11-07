package server

import (
	"crypto/tls"
	"fmt"
	"github.com/chi-chu/bullet-im/src/connect/config"
	"github.com/chi-chu/bullet-im/src/connect/parser"
	"github.com/chi-chu/bullet-im/src/connect/rpc"
	"github.com/chi-chu/bullet-im/src/connect/storage"
	"github.com/chi-chu/log"
	"net"
)

type source struct {
	p		parser.Parser
	s		storage.Storage
	r		rpc.Server
	c		*config.Config
	status	*Status
}

type Status struct {
	ConnectCount		uint64
}

var server *source

func Start(storage storage.Storage, parser parser.Parser, rpc rpc.Server, config *config.Config) error {
	server = &source{p: parser, c: config, s: storage, r: rpc, status:&Status{}}
	return listenTcp()
}

func listenTcp() error {
	addr := fmt.Sprintf("0.0.0.0:%d", server.c.Port)
	listen, err := net.Listen("tcp", addr)
	if err != nil {
		return err
	}
	log.Info("server listen at %s...", addr)
	for {
		conn, err := listen.(*net.TCPListener).AcceptTCP()
		if err != nil {
			log.Error("accept err: %s", err)
			continue
		}
		log.Info("handle new connection, remote address:", conn.RemoteAddr())
		handle(conn)
	}
}

func listenSSL() error {
	cert, err := tls.LoadX509KeyPair(server.c.CertFile, server.c.KeyFile)
	if err != nil {
		return err
	}
	cfg := &tls.Config{Certificates: []tls.Certificate{cert}}
	addr := fmt.Sprintf(":%d", server.c.Port)
	listen, err := tls.Listen("tcp", addr, cfg)
	if err != nil {
		return err
	}
	log.Info("server ssl listen at %s...", addr)
	for {
		conn, err := listen.(*net.TCPListener).AcceptTCP()
		if err != nil {
			log.Error("ssl accept err: %v", err)
			continue
		}
		log.Debug("handle new ssl connection,  remote address: %+v", conn.RemoteAddr())
		handle(conn)
	}
}

func handle(conn *net.TCPConn) {
	c := newClient(newImpl(conn))
	c.Run()
}