const posts = [
    { user: "Alice", text: "This is the first post!" },
    { user: "Bob", text: "Another interesting post appears here." },
    { user: "Charlie", text: "Here's a motivational quote for your day!" },
    { user: "Dana", text: "JavaScript makes everything interactive!" },
  ];

  const postContainer = document.getElementById("post-container");

  function showPosts() {
    postContainer.innerHTML = ""; // Clear previous posts
    posts.forEach((post) => {
      const postElement = document.createElement("div");
      postElement.className = "post";
      postElement.innerHTML = `<strong>${post.user}</strong>: ${post.text}`;
      postContainer.appendChild(postElement);
    });
  }

  function login() {
    alert("Login functionality will be implemented soon!");
  }

  window.onload = showPosts;
