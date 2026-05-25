
  function updateDateTime() {
    const now = new Date();
    const greeting = document.getElementById("greeting-text");
    const datetime = document.getElementById("datetime-text");

    const hours = now.getHours();
    let greetMsg = "Selamat datang";

    if (hours >= 5 && hours < 12) {
      greetMsg = "Selamat pagi";
    } else if (hours >= 12 && hours < 17) {
      greetMsg = "Selamat siang";
    } else if (hours >= 17 && hours < 20) {
      greetMsg = "Selamat sore";
    } else {
      greetMsg = "Selamat malam";
    }

    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateStr = now.toLocaleDateString('id-ID', options);
    const timeStr = now.toLocaleTimeString('id-ID');

    greeting.textContent = greetMsg + " 👋";
    datetime.textContent = `${dateStr}, ${timeStr}`;
  }

  setInterval(updateDateTime, 1000);
  updateDateTime(); // jalankan pertama kali


  document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.dashboard-sidebar');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('active');
    });
  }
});
