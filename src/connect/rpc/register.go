package rpc

import (
	"github.com/chi-chu/bullet-im/src/connect/config"
	"google.golang.org/grpc"
	"net"
	"strconv"
)

type connectServer struct {

}

func InitServer(c *config.Config) error {
	lis, err := net.Listen("tcp", ":"+ strconv.Itoa(c.RpcPort))
	if err != nil {
		return err
	}
	s := grpc.NewServer()
	pb.RegisterXXXXXXfserver(s, &connectServer{})
	go func(){
		if err := s.Serve(lis); err != nil {
			panic(err)
		}
	}()
	return nil
}