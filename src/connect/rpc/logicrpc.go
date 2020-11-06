package rpc

import "google.golang.org/grpc"

type Server interface {
	PushMessage()
}

type logicServer struct {

}

func Init() (Server, error) {
	s := grpc.NewServer()
	return nil, nil
}