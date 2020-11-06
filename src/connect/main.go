package main

import (
	"github.com/chi-chu/bullet-im/src/connect/config"
	"github.com/chi-chu/bullet-im/src/connect/logger"
	"github.com/chi-chu/bullet-im/src/connect/parser"
	"github.com/chi-chu/bullet-im/src/connect/rpc"
	"github.com/chi-chu/bullet-im/src/connect/server"
	"github.com/chi-chu/bullet-im/src/connect/storage"
)

func main() {
	c := config.InitConfig()
	logger.InitLog(c)
	s, err := storage.Init()
	if err != nil {
		panic(err)
	}
	p := parser.Init()
	r, err := rpc.Init()
	if err != nil {
		panic(err)
	}
	err = server.Start(s, p, r, c); if err != nil {
		panic(err)
	}
}