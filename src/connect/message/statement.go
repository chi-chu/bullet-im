package message

const (
	MSG_AUTH				= 0
	MSG_PING				= 1
	MSG_GROUP_CMD			= 2
	MSG_USER_CMD			= 3
	MSG_SYSTEM				= 4
)

const (
	GROUP_CMD_ADD			= 0
	GROUP_CMD_LEAVE 		= 1
	GROUP_CMD_MSG			= 2
)

const (
	USER_MSG				= 0
	USER_CMD_ADD			= 1
	USER_CMD_DEL			= 2
)

const (
	SYSTEM_NOTICE			= 0
)