package main

import (
	"github.com/chi-chu/bullet-im/src/connect/parser"
	"github.com/chi-chu/bullet-im/src/connect/rpc"
	"github.com/chi-chu/bullet-im/src/connect/server"
	"github.com/chi-chu/bullet-im/src/connect/storage"
)

func main() {
	s, err := storage.Init()
	if err != nil {
		panic(err)
	}
	p, err := parser.Init()
	if err != nil {
		panic(err)
	}
	r, err := rpc.Init()
	if err != nil {
		panic(err)
	}
	err = server.Start(s, p, r); if err != nil {
		panic(err)
	}
}