<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sohbet Uygulaması</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: white;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .sent {
            background: #DCF8C6;
            align-self: flex-end;
        }

        .received {
            background: #FFF;
            align-self: flex-start;
        }

        #chat-input {
            display: flex;
            padding: 10px;
            background: #eee;
            border-top: 1px solid #ccc;
        }

        #chat-input input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        #chat-input button {
            padding: 10px;
            border-radius: 50%;
            border: none;
            background: #4CAF50;
            color: white;
            cursor: pointer;
        }

        #chat-input button:hover {
            background: #45a049;
        }

        @media (max-width: 768px) {
            #chat-box {
                padding: 10px;
            }

            #chat-input input[type="text"] {
                font-size: 14px;
            }

            #chat-input button {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div id="chat-box">
        <!-- Mesajlar buraya dinamik olarak yüklenecek -->
    </div>
    <div id="chat-input">
        <input type="text" id="message-input" placeholder="Mesajınızı yazın...">
        <button id="send-button">➤</button>
        <button id="emoji-button">😊</button>
        <button id="record-button">🎤</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const userId = <?php echo $_SESSION['kullanici_id']; ?>; // Kullanıcı ID'si
        const chatWith = 2; // Karşı tarafın ID'si

        function fetchMessages() {
            $.get('fetch_messages.php', { chat_with: chatWith }, function(data) {
                const messages = JSON.parse(data);
                const chatBox = $('#chat-box');
                chatBox.html('');
                messages.forEach(msg => {
                    const alignment = msg.sender_id == userId ? 'sent' : 'received';
                    const content = msg.message_type === 'text' ? msg.message : `<img src="${msg.message}" alt="Resim">`;
                    chatBox.append(`<div class="message ${alignment}">${content}</div>`);
                });
                chatBox.scrollTop(chatBox[0].scrollHeight);
            });
        }

        $('#send-button').on('click', function() {
            const message = $('#message-input').val();
            if (message.trim() !== '') {
                $.post('send_message.php', { receiver_id: chatWith, message, message_type: 'text' }, function() {
                    $('#message-input').val('');
                    fetchMessages();
                });
            }
        });

        setInterval(fetchMessages, 1000);
    </script>
</body>
</html>
