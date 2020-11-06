package rpc

type Server interface {
	PushMessage()
}


func Init() (Server, error) {
	return nil, nil
}