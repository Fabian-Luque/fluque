from websocket import create_connection
ws = create_connection("ws://localhost:3000")
print "Sending 'Hello, World'..."

ws.send("holaaaa")
print "enviado!!"
ws.close()