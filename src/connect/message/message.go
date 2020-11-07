package message


type Message struct {
	Cmd				int
	Version			int
	Data			[]byte
}

func NewMsg() *Message {
	return &Message{}
}

func NewPingMsg() *Message {
	return &Message{
		Cmd:     	MSG_PING,
		Version: 	0,
		Data:		pingMsg(),
	}
}