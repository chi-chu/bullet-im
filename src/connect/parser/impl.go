package parser

import "github.com/chi-chu/bullet-im/src/connect/message"

type p struct {

}

func (p *p) EncodeMessage(msg *message.Message) []byte {

	return nil
}

func (p *p) DecodeMessage(data []byte) *message.Message{

	return &message.Message{}
}
