document.addEventListener("DOMContentLoaded", () => {
  let destinations = {};

  fetch('/hassan/api/get-destinations')
    .then(res => res.json())
    .then(data => {
      console.log("Destinations loaded:", data);
      destinations = data;
      initChatbot(destinations); // Pass destinations to chatbot
    })
    .catch(err => {
      console.error("Failed to load destinations:", err);
    });
});



function initChatbot(destinations) {
  const chatbotIcon = document.getElementById("chatbot-icon");
  const chatbotPopup = document.getElementById("chatbot-popup");
  const closeBtn = document.getElementById("chatbot-close");
  const chatForm = document.getElementById("chat-form");
  const userInput = document.getElementById("user-input");
  const chatMessages = document.getElementById("chat-messages");

  let userData = {};

  // Scroll to bottom helper
  const scrollToBottom = () => {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  };

  // Add message to chat
  const addMessage = (text, sender = "bot") => {
    const message = document.createElement("div");
    message.className = `message ${sender}`;
    message.innerHTML = text;
    chatMessages.appendChild(message);
    scrollToBottom();
  };

  // Typing simulation
  const addTyping = (callback) => {
    const typing = document.createElement("div");
    typing.className = "message bot typing";
    typing.textContent = "Typing...";
    chatMessages.appendChild(typing);
    scrollToBottom();
    setTimeout(() => {
      typing.remove();
      callback();
    }, 700);
  };

  // Button group
  const addButtons = (options, onClick) => {
    const wrapper = document.createElement("div");
    wrapper.className = "message bot";
    options.forEach(opt => {
      const btn = document.createElement("button");
      btn.textContent = opt.label;
      btn.className = "option-button";
      btn.onclick = () => {
        wrapper.remove();
        addMessage(`<strong>You:</strong> ${opt.label}`, "user");
        onClick(opt.value);
      };
      wrapper.appendChild(btn);
    });
    chatMessages.appendChild(wrapper);
    scrollToBottom();
  };

  // Input form (number/date)
  const showInput = (type, placeholder, callback, future = false) => {
    const wrapper = document.createElement("form");
    wrapper.className = "message bot input-form";

    const input = document.createElement("input");
    input.type = type;
    input.placeholder = placeholder;
    input.required = true;

    if (future && type === "date") {
      // Set minimum to today's date
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      input.min = today.toISOString().split('T')[0];
    }

    wrapper.appendChild(input);
    const btn = document.createElement("button");
    btn.textContent = "Send";
    wrapper.appendChild(btn);
    chatMessages.appendChild(wrapper);
    scrollToBottom();

    input.focus();

    wrapper.addEventListener("submit", (e) => {
      e.preventDefault();
      let val = input.value.trim();

      if (type === "tel") {
        // Allow only numbers
        val = val.replace(/\D/g, '');
      }
      if (!val) return;

      addMessage(`<strong>You:</strong> ${val}`, "user");
      wrapper.remove();
      callback(val);
    });
  }

  // Questions Flow
  const askCountry = () => {
    addTyping(() => {
      addMessage("👋 Hello! I'm your Travel Assistant. Where would you like to tour?");
      const options = Object.entries(destinations).map(([slug, data]) => ({
        label: data.name,
        value: slug
      }));
      options.push({ label: "📝 Enter Manually", value: "custom" });

      addButtons(options, (val) => {
        if (val === "custom") {
          userData.manual = true;
          showInput("text", "Enter your destination", (manualDest) => {
            const key = manualDest.trim().toLowerCase();
            if (destinations[key]) {
              userData.destination = key;
              askTravelers();
            } else {
              userData.customDestination = manualDest;
              handleUnknownDestination(manualDest);
            }
          });
        } else {
          userData.manual = false;
          userData.destination = val;
          askTravelers();
        }
      });
    });
  };

  const askTravelers = () => {
    addTyping(() => {
      addMessage("How many people are traveling?");
      showInput("number", "Enter number of travelers", (val) => {
        userData.people = parseInt(val);
        showPackageDetails();
      });
    });
  };

  const handleUnknownDestination = (manualDest) => {
    addTyping(() => {
      addMessage(`
      ❓ Sorry, we don't have a package for "<strong>${manualDest}</strong>" yet.<br>
      Would you like to talk to a travel agent or choose another destination?
    `);

      addButtons([
        { label: "💬 Talk to Agent", value: "agent" },
        { label: "🔁 Choose Another", value: "retry" }
      ], (val) => {
        if (val === "agent") {
          handleAgentCustomContact();
        } else {
          askCountry();
        }
      });
    });
  };

  const showPackageDetails = () => {
    const dest = destinations[userData.destination];
    const totalPrice = dest.price * userData.people;

    addTyping(() => {
      addMessage(`
        <strong>🧳 Trip Summary</strong><br>
        <strong>Destination:</strong> ${dest.name}<br>
        <strong>Duration:</strong> ${dest.duration}<br>
        <strong>Price per person:</strong> $${dest.price.toLocaleString()}<br>
        <strong>Total for ${userData.people}:</strong> <span style="color:green;">$${totalPrice.toLocaleString()}</span>
      `);
      askDate();
    });
  };

  const askDate = () => {
    addTyping(() => {
      addMessage("When do you plan to travel?");
      showInput("date", "Choose travel date", (val) => {
        // Validate that year is 4 digits
        if (!/^\d{4}-\d{2}-\d{2}$/.test(val)) {
          addMessage("❌ Please use the format YYYY‑MM‑DD");
          askDate();
          return;
        }
        const selected = new Date(val);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (selected < today) {
          addMessage("❌ Please select today or a future date.");
          askDate();
          return;
        }
        userData.date = val;
        askFinalChoice();
      }, true);

    });
  };


  const askFinalChoice = () => {
    addMessage("Would you like to book now or talk to an agent?");
    addButtons([
      { label: "📦 Book Now", value: "book" },
      { label: "💬 Talk to Agent", value: "agent" }
    ], (val) => {
      if (val === "book") {
        // In askFinalChoice, after they choose “Book Now”:
        addTyping(() => {
          addMessage("How would you like us to contact you?");
          addButtons([
            { label: "📧 Email", value: "email" },
            { label: "📞 Phone", value: "phone" }
          ], (method) => {
            userData.contactMethod = method;
            handleBooking();
          });
        });
      } else {
        handleAgentContact();
      }
    });
  };

  const handleBooking = () => {
    addMessage("Great! Let's get your booking details.");

    showInput("text", "Enter your full name", (name) => {
      userData.name = name;

      const askPhone = () => showInput("number", "Enter your phone number", (phone) => {
        phone = phone.replace(/\D/g, '');
        if (phone.length < 10) {
          addMessage("❌ Please enter a valid phone number.");
          askPhone();
          return;
        }
        userData.phone = phone;
        showConfirmation();
      });

      if (!userData.manual) {
        if (userData.contactMethod === "email") {
          showInput("email", "Enter your email address", (email) => {
            userData.email = email;
            showConfirmation();
          });
        } else if (userData.contactMethod === "phone") {
          askPhone();
        }
      } else {
        askPhone();
      }
    });
  };

  function showConfirmation() {
    addMessage(`
    ✅ Booking Confirmed!<br>
    <strong>Name:</strong> ${userData.name}<br>
    ${userData.email ? `<strong>Email:</strong> ${userData.email}<br>` : ''}
    ${userData.phone ? `<strong>Phone:</strong> ${userData.phone}<br>` : ''}
    <strong>Destination:</strong> ${userData.manual ? userData.customDestination : destinations[userData.destination].name}<br>
    <strong>Travel Date:</strong> ${userData.date}<br>
    <strong>Total Price:</strong> $${(destinations[userData.destination]?.price * userData.people || 0).toLocaleString()}
  `);
    addMessage("📧 You'll receive a confirmation shortly. Thank you for booking with us!");
  }

  const handleAgentContact = () => {
    addMessage("Sure! We just need a few details to connect you with a travel agent.");

    showInput("text", "Enter your name", (name) => {
      userData.name = name;

      showInput("number", "Enter your phone number", (method) => {
        userData.contactMethod = method;

        showInput("text", "Add any notes or questions", (message) => {
          userData.agentMessage = message;

          addMessage(`
          💬 Thank you, ${userData.name}.<br>
          A travel agent will contact you via <strong>${userData.contactMethod}</strong> soon.<br>
          <strong>Your Message:</strong> ${userData.agentMessage}
        `);
        });
      });
    });
  };

  const handleAgentCustomContact = () => {
    addMessage("Okay! We'll collect a few details to connect you with a travel agent.");

    showInput("number", "How many people are traveling?", (people) => {
      userData.people = parseInt(people);

      showInput("date", "Select your travel date", (date) => {
        userData.date = date;

        showInput("text", "Enter your name", (name) => {
          userData.name = name;

          showInput("number", "Enter your phone number", (method) => {
            userData.contactMethod = method;

            showInput("text", "Any message or special request?", (msg) => {
              userData.agentMessage = msg;

              addMessage(`
              💬 Thank you, ${userData.name}.<br>
              Our travel agent will contact you via <strong>${userData.contactMethod}</strong> soon.<br>
              <strong>Destination:</strong> ${userData.customDestination}<br>
              <strong>Date:</strong> ${userData.date}<br>
              <strong>Travelers:</strong> ${userData.people}<br>
              <strong>Message:</strong> ${userData.agentMessage}
            `);
            });
          });
        });
      });
    });
  };


  // Icon click
  chatbotIcon.addEventListener("click", () => {
    chatbotPopup.classList.add("active");
    if (chatMessages.children.length === 0) {
      askCountry();
    }
  });

  // Close button
  closeBtn.addEventListener("click", () => {
    chatbotPopup.classList.remove("active");

    // Clear messages
    chatMessages.innerHTML = "";

    // Reset user data
    userData = {};

    // Re-initialize after closing
    setTimeout(() => {
      askCountry(); // restart flow when reopened
    }, 300); // small delay to ensure it works on reopen
  });

  document.getElementById("new-chat-btn").addEventListener("click", () => {
    chatMessages.innerHTML = "";
    userData = {};
    askCountry();
  });



  // Prevent default submit
  chatForm.addEventListener("submit", (e) => {
    e.preventDefault();
    userInput.value = "";
  });
};
