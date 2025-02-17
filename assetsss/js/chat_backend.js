const firebaseConfig = {
    apiKey: "AIzaSyCwqfgGe4ROqAr6tl35UuyjhHG1BqYcMG8",
    authDomain: "land-mapping-54dde.firebaseapp.com",
    databaseURL: "https://land-mapping-54dde-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "land-mapping-54dde",
    storageBucket: "land-mapping-54dde.firebasestorage.app",
    messagingSenderId: "1050471575275",
    appId: "1:1050471575275:web:993edc75d47486d7cb98ae"
};

const app = firebase.initializeApp(firebaseConfig);
const database = firebase.database(app);

const messagesRef = database.ref('chat/messages');

function displayMessages(messages) {
    const conversationWrapper = document.querySelector('.conversation-wrapper');
    conversationWrapper.innerHTML = '';

    messages.forEach(message => {
        const messageItem = document.createElement('li');
        messageItem.classList.add('conversation-item');
        messageItem.innerHTML = `
        <div class="conversation-item-side">
            <img class="conversation-item-image" src="${message.userImage}" alt="">
        </div>
        <div class="conversation-item-content">
            <div class="conversation-item-wrapper">
                <div class="conversation-item-box">
                    <div class="conversation-item-text">
                        <p>${message.text}</p>
                        <div class="conversation-item-time">${message.timestamp}</div>
                    </div>
                </div>
            </div>
        </div>
        `;
        conversationWrapper.appendChild(messageItem);
    });
}

// Listen for real-time updates from Firebase
messagesRef.on('value', (snapshot) => {
    const messages = snapshot.val() || [];
    displayMessages(Object.values(messages));  // Convert object to array
});

// Send message when button is clicked
document.querySelector('.conversation-form-submit').addEventListener('click', () => {
    const messageInput = document.querySelector('.conversation-form-input');
    const messageText = messageInput.value.trim();
    
    if (messageText) {
        const messageData = {
            user: 'UserName',  // Replace with dynamic user data
            userImage: 'https://example.com/user-image.jpg',  // Replace with dynamic user image
            text: messageText,
            timestamp: new Date().toLocaleTimeString(),
        };
        messagesRef.push(messageData);  // Push new message to Firebase
        messageInput.value = '';  // Clear input field
    }
});