export class Month {
  constructor() {
    this.nav = 0;
    this.holdStarter = null;
    this.holdDelay = 350;
    this.holdActive = false;
    this.holded = false;
    this.currentYear = 0;
    this.currentMonth = 0;
    this.currentDay = 0;
    this.firstDayOfMonth = null;
    this.currentDate = null;
    this.daysInMonth = 0;
    this.sundayFirst = false;
    this.previousMonthLength = 0;
    this.events = [];
    this.dateFrom = "";
    this.daysLabels = [
      "Lundi",
      "Mardi",
      "Mercredi",
      "Jeudi",
      "Vendredi",
      "Samedi",
      "Dimanche",
    ];
    this.monthsLabels = [
      "Janvier",
      "Fevrier",
      "Mars",
      "Avril",
      "Mai",
      "Juin",
      "Juillet",
      "Aout",
      "Septembre",
      "Octobre",
      "Novembre",
      "Decembre",
    ];
  }

  // METHOD

  async load() {
    this.initDate();

    this.initMonthInNavBar();

    this.initNavBar();

    this.initButtons();
    // Rempli les event grace au fetch
    this.events = await this.getAllEvents();
    await this.initCalendar();

    this.initAddIntervention();

    this.initEventModal();
  }

  async read() {
    this.initDate();

    this.initMonthInNavBar();

    this.initNavBar();

    this.initButtonsRead();
    // Rempli les event grace au fetch
    this.events = await this.getAllEvents();
    await this.initCalendar();

    this.initEventModal();
  }

  // Initialise la date
  initDate() {
    this.currentDate = new Date();
    this.currentDay = this.currentDate.getDate();
    this.currentYear = this.currentDate.getFullYear();
  }

  // Initialise la navbar du planning
  initNavBar() {
    // test if input date is correct, instead use current month
    this.month =
      isNaN(this.currentMonth) || this.currentMonth == null
        ? this.currentDate.getMonth() + 1
        : this.currentMonth;
    this.year =
      isNaN(this.currentYear) || this.currentYear == null
        ? this.currentDate.getFullYear()
        : this.currentYear;
    // Get element for inject
    const action_schedule = document.getElementById("action-schedule");
    action_schedule.innerHTML = "";
    // Creation de la navbar
    const info_schedule = document.createElement("ul");
    info_schedule.setAttribute("class", "pagination");
    info_schedule.setAttribute("id", "info-schedule");
    const currentMonth = document.createElement("li");
    currentMonth.setAttribute("class", "page-item");
    currentMonth.setAttribute("id", "currentMonth");
    currentMonth.setAttribute("data", this.monthsLabels[this.currentMonth]);
    const aCurrent = document.createElement("a");
    aCurrent.setAttribute("class", "page-link");
    aCurrent.innerHTML =
      this.monthsLabels[this.currentMonth] + " - " + this.currentYear;
    const aNext = document.createElement("a");
    aNext.setAttribute("class", "page-link");
    const nextMonth = document.createElement("li");
    nextMonth.setAttribute("class", "page-item");
    nextMonth.setAttribute("type", "button");
    nextMonth.setAttribute("id", "nextButton");
    aNext.innerHTML = ">";
    const aPrev = document.createElement("a");
    aPrev.setAttribute("class", "page-link");
    const prevMonth = document.createElement("li");
    prevMonth.setAttribute("class", "page-item");
    prevMonth.setAttribute("type", "button");
    prevMonth.setAttribute("id", "prevButton");
    aPrev.innerHTML = "<";

    // Injection

    prevMonth.appendChild(aPrev);
    nextMonth.appendChild(aNext);
    currentMonth.appendChild(aCurrent);

    info_schedule.appendChild(prevMonth);
    info_schedule.appendChild(currentMonth);
    info_schedule.appendChild(nextMonth);

    action_schedule.appendChild(info_schedule);
  }

  // Initialise le mois selon la navbar
  initMonthInNavBar() {
    if (this.nav !== 0) {
      this.currentMonth = this.currentDate.getMonth() + this.nav;
      if (this.currentMonth > 11) {
        this.currentYear =
          this.currentYear + Math.floor(this.currentMonth / 12);
        this.currentMonth = Math.floor(this.currentMonth % 12);
      } else if (this.currentMonth < 0) {
        this.currentYear =
          this.currentYear + Math.floor(this.currentMonth / 12);
        this.currentMonth = Math.floor(this.currentMonth % -12) + 12;
        if (this.currentMonth === 12) {
          this.currentMonth = 0;
        }
      } else {
        this.currentMonth = this.currentDate.getMonth() + this.nav;
      }
    } else {
      this.currentMonth = this.currentDate.getMonth();
    }

    this.firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1);
    this.daysInMonth = new Date(
      this.currentYear,
      this.currentMonth + 1,
      0
    ).getDate();
  }

  // Init button
  initButtons() {
    document.getElementById("nextButton").addEventListener("click", () => {
      // console.log(this.nav);
      if (this.isLoad) {
        console.log("Loading...");
      } else {
        this.nav++;
        this.load();
      }
    });

    document.getElementById("prevButton").addEventListener("click", () => {
      // console.log(this.nav);

      if (this.isLoad) {
        console.log("Loading...");
      } else {
        this.nav--;
        this.load();
      }
    });
  }

  // Init button
  initButtonsRead() {
    document.getElementById("nextButton").addEventListener("click", () => {
      // console.log(this.nav);
      if (this.isLoad) {
        console.log("Loading...");
      } else {
        this.nav++;
        this.read();
      }
    });

    document.getElementById("prevButton").addEventListener("click", () => {
      // console.log(this.nav);

      if (this.isLoad) {
        console.log("Loading...");
      } else {
        this.nav--;
        this.read();
      }
    });
  }

  // Recuperer toutes les interventions en JSON
  async getAllEvents() {
    const response = await fetch("/events");
    const events = await response.json();

    return events;
  }

  // Create the calendar in HTML
  async initCalendar() {
    const schedule = document.getElementById("schedule");
    schedule.innerHTML = "";

    // Recupérer le premier jour du mois et le premier jour (Lundi..) de la premiere semaine du mois
    let firstDayWeekDay =
      this.firstDayOfMonth.getDay() == 0 ? 7 : this.firstDayOfMonth.getDay();
    // Recupere la longueur du mois précédent
    this.previousMonthLength = new Date(this.year, this.month, 0).getDate();
    // Creation de la div scrollable
    let scroll = document.createElement("div");
    scroll.setAttribute("id", "scroll");
    scroll.setAttribute("class", "overflow-scroll");

    // Creation des libellés
    for (let i = 0; i <= 6; i++) {
      const dayLabel = document.createElement("div");
      dayLabel.setAttribute("class", "dayLabel overflow-hidden p-1");
      dayLabel.innerText = this.daysLabels[i];
      scroll.appendChild(dayLabel);
    }

    // Variable
    let prev = 1;
    let day = 1;
    let next = 1;
    // Creation du Calendrier
    for (let i = 1; i <= 42; i++) {
      const daySquare = document.createElement("div");
      let daydata = day < 10 ? "0" + day : day;
      let month = this.month + 1 < 10 ? "0" + (this.month + 1) : this.month + 1;
      let dayAttribute = "";

      if (i < firstDayWeekDay) {
        let dayNb = this.previousMonthLength - firstDayWeekDay + 1 + prev;
        dayAttribute = dayNb + "-" + (month - 1) + "-" + this.year;
        daySquare.setAttribute("class", "dayOtherMonth overflow-hidden p-1");
        daySquare.setAttribute("data-date", dayAttribute);
        daySquare.innerText = dayNb;
        scroll.appendChild(daySquare);
        prev++;
      } else if (day <= this.daysInMonth) {
        dayAttribute = daydata + "-" + month + "-" + this.year;
        const eventsForDay = [];
        this.events.forEach((event) => {
          if (
            event.dateDebut.substr(0, 10) === dayAttribute ||
            event.dateFin.substr(0, 10) === dayAttribute
          ) {
            eventsForDay.push(event);
          } else if (
            this.cmpDateString(event.dateDebut.substr(0, 10), dayAttribute) &&
            this.cmpDateString(dayAttribute, event.dateFin.substr(0, 10))
          ) {
            eventsForDay.push(event);
          }
        });

        if (
          day == this.currentDay &&
          this.currentMonth == this.currentDate.getMonth() &&
          this.currentYear == this.currentDate.getFullYear()
        ) {
          daySquare.setAttribute("id", "today");
        }
        const dayNb = document.createElement("div");
        dayNb.setAttribute("data-date", dayAttribute);
        dayNb.setAttribute("class", "dayNb");
        dayNb.innerText = day;
        daySquare.appendChild(dayNb);

        if (eventsForDay) {
          if (eventsForDay.length > 2) {
            const eventDiv = document.createElement("div");
            eventDiv.setAttribute(
              "class",
              "event badge bg-dark mw-100 overfow-hidden d-flex flex-wrap"
            );
            eventDiv.setAttribute("data-date", dayAttribute);
            eventDiv.style.maxWidth = "50px";
            let eventP = document.createElement("span");
            eventP.setAttribute("data-date", dayAttribute);
            eventP.setAttribute("class", "spanEvent overflow-hidden");
            eventP.innerText = "Voir +";

            eventDiv.appendChild(eventP);
            daySquare.appendChild(eventDiv);
          } else {
            eventsForDay.forEach((event) => {
              const eventDiv = document.createElement("div");

              eventDiv.setAttribute(
                "class",
                "event badge bg-dark mw-100 overfow-hidden d-flex flex-wrap mb-1"
              );
              eventDiv.setAttribute("data-date", dayAttribute);
              eventDiv.style.maxWidth = "50px";
              let eventP = document.createElement("span");
              eventP.setAttribute("data-date", dayAttribute);
              eventP.setAttribute("class", "spanEvent overflow-hidden");

              let entreprises = "";

              event.users.forEach((user) => {
                if (event.users.length > 1) {
                  entreprises = "1 et +";
                } else {
                  entreprises = user.entreprise;
                }
              });

              eventP.innerText =
                entreprises.substr(0, 10) + " : " + event.sujet.substr(0, 12);
              eventDiv.appendChild(eventP);
              daySquare.appendChild(eventDiv);
            });
          }
        }

        daySquare.classList.add();
        daySquare.setAttribute("class", "day overflow-hidden p-1");
        daySquare.setAttribute("data-date", dayAttribute);

        scroll.appendChild(daySquare);
        day++;
      } else {
        dayAttribute = next + "-" + (month - 1) + "-" + this.year;
        daySquare.setAttribute("class", "overflow-hidden p-1");
        daySquare.classList.add("dayOtherMonth");
        daySquare.setAttribute("data-date", dayAttribute);
        daySquare.innerText = next;
        scroll.appendChild(daySquare);
        next++;
      }
    }
    schedule.appendChild(scroll);
  }

  // add Event Click for open modal from day
  initEventModal() {
    let events = document.querySelectorAll(".event");
    let THAT = this;

    events.forEach((event) => {
      event.addEventListener("mousedown", (event) => {
        let dayT = event.target.getAttribute("data-date");
        THAT.openModalEvent(dayT);
        event.stopPropagation();
      });
    });
    if (document.getElementById("closeButton")) {
      document.getElementById("closeButton").addEventListener("click", (e) => {
        e.preventDefault();
        this.closeModal();
      });
    }
  }

  // Add Event Click on Event in calendar
  initAddIntervention() {
    if (document.getElementById("closeButton")) {
      document.getElementById("closeButton").addEventListener("click", (e) => {
        e.preventDefault();
        this.closeModalForm();
      });
    }

    let days = document.querySelectorAll("div.day");
    days.forEach((day) => {
      day.addEventListener("mousedown", (e) => {
        this.onMouseDown(e);
      });

      day.addEventListener("mouseup", (e) => {
        this.onMouseUp(e);
      });
      day.addEventListener("mouseenter", (e) => {
        this.onMouseEnter(e);
      });
    });
  }

  // Permet de savoir si le click est maintenue ou non.
  onMouseDown(e) {
    let THAT = this;
    let date = e.target.getAttribute("data-date");
    console.log(date);
    this.dateFrom = date;
    this.holdStarter = setTimeout(function () {
      this.holdStarter = null;
      this.holdActive = true;

      let daysSquare = document.querySelectorAll("div.day");
      daysSquare.forEach((daySquare) => {
        daySquare.classList.add("clicked");
        if (daySquare.getAttribute("data-date") == date) {
          daySquare.style.backgroundColor = "orange";
        }
      });
      console.log("Holding...");
      THAT.setDateFrom(date);
      THAT.holded = true;
    }, this.holdDelay);
  }

  // permet de savoir quand le click est relaché
  onMouseUp(e) {
    if (this.holdStarter) {
      clearTimeout(this.holdStarter);
      // Entrer le jour cliqué dans le formulaire
      let date = e.target.getAttribute("data-date");
      let daysSquare = document.querySelectorAll("div.day");

      if (this.holded) {
        this.setDateTo(date);
      } else {
        this.setDateFrom(date);
      }
      this.openModalForm();
      this.holded = false;
      daysSquare.forEach((daySquare) => {
        daySquare.classList.remove("clicked");
        if (daySquare.style.backgroundColor == "orange") {
          daySquare.style.backgroundColor = "#efefef";
        }
        if (daySquare.getAttribute("id") == "today") {
          daySquare.style.backgroundColor = "rgb(119, 176, 176)";
        }
      });
    } else if (this.holdActive) {
      daySquare.classList.remove("clicked");
      this.holdActive = false;
      console.log("Holded");
    }
  }

  onMouseEnter(e) {
    if (this.holded) {
      console.log(e.target);
      let date = e.target.getAttribute("data-date");
      console.log(date);
      let daysSquare = document.querySelectorAll("div.day");
      console.log(this.dateFrom);
      console.log(this.dateStringEn(this.dateFrom));
      console.log(this.dateStringEn(date));
      daysSquare.forEach((daySquare) => {
        if (
          this.dateStringEn(this.dateFrom) < this.dateStringEn(date) ||
          this.dateStringEn(this.dateFrom) == this.dateStringEn(date)
        ) {
          if (
            (this.dateStringEn(daySquare.getAttribute("data-date")) >
              this.dateStringEn(this.dateFrom) &&
              this.dateStringEn(daySquare.getAttribute("data-date")) <
                this.dateStringEn(date)) ||
            this.dateStringEn(daySquare.getAttribute("data-date")) ==
              this.dateStringEn(date) ||
            this.dateStringEn(daySquare.getAttribute("data-date")) ==
              this.dateStringEn(this.dateFrom)
          ) {
            daySquare.style.backgroundColor = "orange";
          } else {
            if (
              daySquare.getAttribute("class") != "dayOtherMonth" &&
              daySquare.getAttribute("id") == "today"
            ) {
              daySquare.style.backgroundColor = "rgb(119, 176, 176)";
            } else {
              daySquare.style.backgroundColor = "#efefef";
            }
          }
        }
      });
    }
  }

  // Rempli le formulaire : Date de debut
  setDateFrom(date) {
    let dateFrom = document.getElementById("dateFrom");
    dateFrom.setAttribute(
      "value",
      this.dateStringEn(date).replace(/\//g, "-") + "T08:00"
    );
  }

  // Rempli le formulaire : Date de fin
  setDateTo(date) {
    let dateTo = document.getElementById("dateTo");
    if (
      this.dateStringEn(date) > this.dateStringEn(this.dateFrom) ||
      this.dateStringEn(date) == this.dateStringEn(this.dateFrom)
    ) {
      dateTo.setAttribute(
        "value",
        this.dateStringEn(date).replace(/\//g, "-") + "T20:00"
      );
    }
  }

  // Ouvre le modal de chaque jour
  openModalEvent(date) {
    // Contruct Modal
    let modal = document.getElementById("modal");
    modal.innerHTML = "";
    modal.style.display = "block";
    let modalContainer = document.createElement("div");
    modalContainer.setAttribute("class", "vh-100 d-flex align-items-center");
    let modalDiv = document.createElement("div");
    modalDiv.setAttribute(
      "class",
      "modalDiv card col-9 col-md-8 mx-auto my-auto d-flex"
    );
    let closeDiv = document.createElement("div");
    closeDiv.setAttribute("class", "d-flex justify-content-end");
    let close = document.createElement("button");
    close.setAttribute("id", "closeButton");
    close.setAttribute("type", "button");
    close.setAttribute("class", "btn-close");
    close.setAttribute("aria-label", "Close");
    close.innerHTML = "";

    modal.addEventListener("click", (e) => {
      e.preventDefault();
      let element = e.target;
      if (element.id == "closeButton") {
        this.closeModal();
      }
    });

    let headModal = document.createElement("h1");
    headModal.setAttribute("class", "text-center mb-5");
    headModal.innerHTML = "Intervention(s) du " + date;
    ///

    let labelModal = document.createElement("div");
    labelModal.setAttribute(
      "class",
      "d-flex flex-row justify-content-around text-center border border-dark text-white bg-gray"
    );
    labelModal.innerHTML =
      '<div class="col my-2 d-none d-lg-inline"><strong>N°</strong></div><div class="col my-2"><strong>Entreprise</strong></div><div class="col my-2"><strong>Sujet</strong></div><div class="col my-2"><strong>Date de Début</strong></div><div class="col my-2"><strong>Date de fin</strong></div>';

    ///

    modalDiv.appendChild(closeDiv);
    modalDiv.appendChild(headModal);
    modalDiv.appendChild(labelModal);
    ///
    let i = 0;
    ///
    this.events.forEach((event) => {
      if (
        (this.cmpDateString(event.dateDebut, date) &&
          this.cmpDateString(date, event.dateFin)) ||
        this.cmpDateString(date, event.dateDebut, true) ||
        this.cmpDateString(date, event.dateFin, true)
      ) {
        let infoModal = document.createElement("div");
        if (i == 0) {
          infoModal.setAttribute(
            "class",
            "d-flex flex-row justify-content-around text-center border border-dark"
          );
          i++;
        } else {
          infoModal.setAttribute(
            "class",
            "d-flex flex-row justify-content-around text-center border border-dark  bg-lightgray"
          );
          i--;
        }

        let contentModal = "";
        let id = "";
        let societyInfo = "";
        let sujet = "";
        let dateFin = "";
        let dateDebut = "";

        for (const property in event) {
          switch (property) {
            case "users":
              societyInfo += "<p class='col my-2'>";
              let i = 0;
              event.users.forEach((user) => {
                if (event.users.length > 1) {
                  // if (i == 0) {
                  societyInfo += user.entreprise.substr(0, 8) + "<br>";
                  // }
                  i = 1;
                } else {
                  societyInfo += user.entreprise.substr(0, 8) + " ";
                }
              });
              societyInfo += "</p>";
              break;
            case "sujet":
              sujet = "<p class='col my-auto'>" + event.sujet.substr(0, 10);
              +"</p>";
              break;
            case "dateFin":
              dateFin +=
                "<p class='col my-auto'>" + event.dateDebut.substr(0, 10);
              +"</p>";
              break;
            case "dateDebut":
              dateDebut +=
                "<p class='col my-auto'>" + event.dateDebut.substr(0, 10);
              +"</p>";
              break;
            case "id":
              id += "<p class='col my-auto d-none d-lg-inline'>" + event.id;
              +"</p>";
              break;
            default:
              break;
          }
        }
        contentModal += id + societyInfo + sujet + dateDebut + dateFin;

        ///
        // render modal
        infoModal.innerHTML = contentModal;
        modalDiv.appendChild(infoModal);
      }
    });

    modalContainer.appendChild(modalDiv);
    modal.appendChild(modalContainer);
    closeDiv.appendChild(close);
    ////
  }

  // Ouvre le modal avec le formulaire pour ajouter une intervention
  openModalForm() {
    let modal = document.getElementById("modalForm");
    modal.style.display = "block";
  }

  // Permet de fermer les modals
  closeModal() {
    let modal = document.getElementById("modal");

    modal.style.display = "none";
  }

  closeModalForm() {
    let modalForm = document.getElementById("modalForm");
    modalForm.style.display = "none";
  }

  // Compare 2 date en string avec l'écriture "DD/MM/YYYY"
  cmpDateString(dateA, dateB, egal = false) {
    let dateEnA = this.dateStringEn(dateA);
    let dateEnB = this.dateStringEn(dateB);

    console.log(dateEnA);
    console.log(dateEnB);

    let d1 = Date.parse(dateEnA);
    let d2 = Date.parse(dateEnB);

    if (egal) {
      if (d1 == d2) {
        return true;
      }
      return false;
    } else {
      if (d1 < d2) {
        return true;
      }

      return false;
    }
  }

  // Transforme une date:string "DD/MM/YYYY" en date:string "YYYY/MM/DD"
  dateStringEn(date) {
    let year = date.substr(6, 4);
    let month = date.substr(3, 2);
    let day = date.substr(0, 2);
    let dateEn = year + "/" + month + "/" + day;
    return dateEn;
  }
}
