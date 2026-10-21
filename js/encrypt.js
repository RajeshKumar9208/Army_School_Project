function encryptLoginForm() {
    
    const key = CryptoJS.enc.Utf8.parse('pL@9xZ#7vTb!dQ3R'); // 16-byte strong key
    const iv = CryptoJS.enc.Utf8.parse('kM^2sG$8bWu@eL1N');  // 16-byte strong IV

    const usernameField = document.getElementById("username");
    const passwordField = document.getElementById("password");

    const encryptedUsername = CryptoJS.AES.encrypt(usernameField.value, key, { iv: iv }).toString();
    const encryptedPassword = CryptoJS.AES.encrypt(passwordField.value, key, { iv: iv }).toString();

    usernameField.value = encryptedUsername;
    passwordField.value = encryptedPassword;

    return true;
}
