// firebase-config.js
if (typeof firebase !== 'undefined' && !firebase.apps.length) {
    firebase.initializeApp({
        apiKey: "AIzaSyBceoCExF8pRVzOjWz0-WzyuduqbmdnSU0",
        authDomain: "thegentlemen-sbar.firebaseapp.com",
        projectId: "thegentlemen-sbar",
        storageBucket: "thegentlemen-sbar.firebasestorage.app",
        messagingSenderId: "837554151313",
        appId: "1:837554151313:web:c6196f61884d7710844430"
    });
}

if (!window.db) {
    window.db = firebase.firestore();
}