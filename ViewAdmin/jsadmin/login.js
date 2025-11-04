  const greeting = document.getElementById('greeting');
  const hour = new Date().getHours();
  let greetText = "";

  if (hour >= 4 && hour < 11) {
    greetText = "Good Morning, Captain 🌞";
  } else if (hour >= 11 && hour < 15) {
    greetText = "Good Afternoon, Captain ☀️";
  } else if (hour >= 15 && hour < 19) {
    greetText = "Good Evening, Captain 🌇";
  } else {
    greetText = "Good Night, Captain 🌙";
  }

  greeting.textContent = greetText;
