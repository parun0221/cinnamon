document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value.trim();
  
  if (username === '' || password === '') {
    alert('Harap isi semua kolom!');
    return;
  }
  
  // Simulasi login
  alert(`Login berhasil!\nUsername: ${username}`);
  
  // Redirect bisa ditambahkan di sini
  // window.location.href = '/dashboard.html';
});
