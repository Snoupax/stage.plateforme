export class Month {
  constructor() {
    this.nav = 0;
    this.holdStarter = null;
    this.holdDelay = 500;
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
    this.events;
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
    // Get element for inject
    const action_schedule = document.getElementById("action-schedule");
    const schedule = document.getElementById("schedule");
    action_schedule.innerHTML = "";
    schedule.innerHTML = "";

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

    this.initButtons();

    // Initialise la date
    this.currentDate = new Date();
    this.currentDay = this.currentDate.getDate();
    this.currentYear = this.currentDate.getFullYear();

    // Initialise le mois selon la navbar
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

    // First Version
    // test if input date is correct, instead use current month
    this.month =
      isNaN(this.currentMonth) || this.currentMonth == null
        ? this.currentDate.getMonth() + 1
        : this.currentMonth;
    this.year =
      isNaN(this.currentYear) || this.currentYear == null
        ? this.currentDate.getFullYear()
        : this.currentYear;

    // Recupérer le premier jour du mois et le premier jour (Lundi..) de la premiere semaine du mois
    let firstDayWeekDay =
      this.firstDayOfMonth.getDay() == 0 ? 7 : this.firstDayOfMonth.getDay();
    // Recupere la longueur du mois précédent
    this.previousMonthLength = new Date(this.year, this.month, 0).getDate();

    aCurrent.innerHTML =
      this.monthsLabels[this.currentMonth] + " - " + this.currentYear;
    // Creation de la div scrollable
    let scroll = document.createElement("div");
    scroll.setAttribute("id", "scroll");
    scroll.setAttribute("class", "overflow-scroll");

    // Creation des libellés
    for (let i = 0; i <= 6; i++) {
      const dayLabel = document.createElement("div");
      dayLabel.classList.add("dayLabel");
      dayLabel.innerText = this.daysLabels[i];
      scroll.appendChild(dayLabel);
    }

    // Rempli les event grace au fetch
    this.events = await this.getEvent();
    // Variable
    let prev = 1;
    let day = 1;
    let next = 1;

    // Creation du Calendrier
    for (let i = 1; i <= 42; i++) {
      const daySquare = document.createElement("div");
      let daydata = day < 10 ? "0" + day : day;
      let month = this.month + 1 < 10 ? "0" + (this.month + 1) : this.month + 1;
      const dayAttribute = daydata + "-" + month + "-" + this.year;

      if (i < firstDayWeekDay) {
        daySquare.classList.add("dayOtherMonth");
        daySquare.innerText =
          this.previousMonthLength - firstDayWeekDay + 1 + prev;
        scroll.appendChild(daySquare);
        prev++;
      } else if (day <= this.daysInMonth) {
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
          daySquare.classList.add("today");
        }
        const dayNb = document.createElement("div");
        dayNb.setAttribute("data-date", dayAttribute);
        dayNb.innerText = day;
        daySquare.appendChild(dayNb);

        if (eventsForDay) {
          if (eventsForDay.length > 2) {
            const eventDiv = document.createElement("div");
            eventDiv.setAttribute(
              "class",
              "event badge bg-dark mw-100 overfow-hidden "
            );
            eventDiv.setAttribute("data-date", dayAttribute);
            eventDiv.style.maxWidth = "50px";
            eventDiv.innerText = "Voir +";
            daySquare.appendChild(eventDiv);
          } else {
            eventsForDay.forEach((event) => {
              const eventDiv = document.createElement("div");
              eventDiv.setAttribute(
                "class",
                "event badge bg-dark mw-100 overfow-hidden"
              );
              eventDiv.setAttribute("data-date", dayAttribute);
              eventDiv.style.maxWidth = "50px";
              eventDiv.innerText =
                event.user.entreprise.substr(0, 4) +
                " : " +
                event.sujet.substr(0, 12);
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
        daySquare.classList.add("dayOtherMonth");
        daySquare.innerText = next;
        scroll.appendChild(daySquare);
        next++;
      }
    }
    schedule.appendChild(scroll);
    this.initAddEvent();
    this.initEventAddEvent();
  }

  // Init button
  initButtons() {
    document.getElementById("nextButton").addEventListener("click", () => {
      console.log(this.nav);
      if (this.isLoad) {
        console.log("Loading...");
      } else {
        this.nav++;
        this.load();
      }
    });

    document.getElementById("prevButton").addEventListener("click", () => {
      console.log(this.nav);

      if (this.isLoad) {
        console.log("Loading...");
      } else {
        this.nav--;
        this.load();
      }
    });
    document.getElementById("closeButton").addEventListener("click", (e) => {
      e.preventDefault();
      this.closeModal();
    });
    document.getElementById("valid").addEventListener("click", (e) => {});
  }

  initEventAddEvent() {
    let events = document.querySelectorAll(".event");
    let THAT = this;

    events.forEach((event) => {
      event.addEventListener("mousedown", (event) => {
        console.log(event.target);
        let dayT = event.target.getAttribute("data-date");
        THAT.openModalEvent(dayT);
        console.log("Click sur Event " + dayT);
        event.stopPropagation();
      });
    });
  }

  initAddEvent() {
    let days = document.querySelectorAll("div.day");
    days.forEach((day) => {
      day.addEventListener("mousedown", (e) => {
        this.onMouseDown(e);
        console.log(day);
      });

      day.addEventListener("mouseup", (e) => {
        this.onMouseUp(e);
        console.log(day);
      });
    });
  }

  onMouseDown(e) {
    let THAT = this;
    console.log(e.target.id);
    this.holdStarter = setTimeout(function () {
      this.holdStarter = null;
      this.holdActive = true;
      let date = e.target.getAttribute("data-date");
      console.log(date);
      console.log("Holding...");
      THAT.setDateFrom(date);
      THAT.holded = true;
    }, this.holdDelay);
  }

  onMouseUp(e) {
    if (this.holdStarter) {
      clearTimeout(this.holdStarter);
      // Entrer le jour cliqué dans le formulaire
      let date = e.target.getAttribute("data-date");
      console.log(date);
      console.log(this.holded);
      if (this.holded) {
        this.setDateTo(date);
      } else {
        this.setDateFrom(date);
        console.log("Click sur jour");
      }
      this.openModalForm();
      this.holded = false;
    } else if (this.holdActive) {
      this.holdActive = false;
      let date = e.target.getAttribute("data-date");
      console.log(date);
      console.log("Holded");
    }
  }

  openModalEvent(date) {
    // Contruct Modal
    let modal = document.getElementById("modal");
    modal.innerHTML = "";
    let modalContainer = document.createElement("div");
    modalContainer.setAttribute("class", "vh-100 d-flex align-items-center");
    let modalDiv = document.createElement("div");
    modalDiv.setAttribute("class", "card col-6 mx-auto my-auto d-flex");
    let closeDiv = document.createElement("div");
    let close = document.createElement("button");
    close.setAttribute("id", "closeButton");
    close.innerHTML = "X";

    modal.addEventListener("click", (e) => {
      e.preventDefault();
      let element = e.target;
      if (element.id == "closeButton") {
        this.closeModal();
      }
    });

    this.events.forEach((event) => {
      if (
        (this.cmpDateString(event.dateDebut, date) &&
          this.cmpDateString(date, event.dateFin)) ||
        this.cmpDateString(date, event.dateDebut, true) ||
        this.cmpDateString(date, event.dateFin, true)
      ) {
        modal.style.display = "block";
        let headModal = document.createElement("h1");
        headModal = "Liste du " + date;
        let infoModal = document.createElement("div");
        let contentModal = "";
        contentModal += "Intervention " + event.id + " <br>";
        for (const property in event) {
          if (property == "user") {
            contentModal +=
              "<p class='card-text'> Entreprise : " +
              event.user.entreprise +
              "</p>";
          } else if (property == "messageOptionnel") {
          } else {
            contentModal +=
              "<p class='card-text'>" +
              property +
              " : " +
              event[property] +
              "</p>";
          }
        }

        // render modal
        infoModal.innerHTML = contentModal;

        modalDiv.appendChild(infoModal);
        modalDiv.appendChild(closeDiv);
        modalContainer.appendChild(modalDiv);
        modal.appendChild(modalContainer);
        closeDiv.appendChild(close);
      }
    });
  }

  setDateFrom(date) {
    let dateFrom = document.getElementById("dateFrom");
    dateFrom.setAttribute(
      "value",
      this.dateStringEn(date).replace(/\//g, "-") + "T08:00"
    );
  }
  setDateTo(date) {
    let dateTo = document.getElementById("dateTo");
    dateTo.setAttribute(
      "value",
      this.dateStringEn(date).replace(/\//g, "-") + "T20:00"
    );
  }

  openModalForm(date) {
    let modal = document.getElementById("modalForm");
    modal.style.display = "block";
  }
  closeModal() {
    let modal = document.getElementById("modal");
    let modalForm = document.getElementById("modalForm");
    modal.style.display = "none";
    modalForm.style.display = "none";
  }

  async getEvent() {
    const response = await fetch("/test");
    const events = await response.json();
    return events;
  }

  cmpDateString(dateA, dateB, egal = false) {
    let dateEnA = this.dateStringEn(dateA);
    let dateEnB = this.dateStringEn(dateB);

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

  dateStringEn(date) {
    let year = date.substr(6, 4);
    let month = date.substr(3, 2);
    let day = date.substr(0, 2);
    let dateEn = year + "/" + month + "/" + day;
    return dateEn;
  }
}
