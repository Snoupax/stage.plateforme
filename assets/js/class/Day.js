export class Day {
  constructor() {
    this.nav = 0;
    this.currentYear = 0;
    this.currentMonth = 0;
    this.currentDay = 0;
    this.firstDayOfMonth = null;
    this.currentDate = null;
    this.daysInMonth = 0;
    this.sundayFirst = false;
    this.previous_month_length = 0;
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

  load() {
    // Get element for inject
    const action_day = document.getElementById("action-day");
    const dayDiv = document.getElementById("day");
    action_day.innerHTML = "";
    dayDiv.innerHTML = "";
    // Initialise la date
    this.currentDate = new Date();
    this.currentYear = this.currentDate.getFullYear();
    let first_day_weekday =
      this.currentDate.getDay() == 0 ? 7 : this.currentDate.getDay();
    // Current Month
    this.currentMonth = this.currentDate.getMonth();
    console.log(this);
    this.currentDay = this.currentDate.getDate();

    this.firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1);
    this.daysInMonth = new Date(
      this.currentYear,
      this.currentMonth + 1,
      0
    ).getDate();

    const info_day = document.createElement("ul");
    info_day.setAttribute("class", "pagination");
    info_day.setAttribute("id", "info-week");
    const currentDay = document.createElement("li");
    currentDay.setAttribute("class", "page-item");
    currentDay.setAttribute("id", "currentMonth");
    const aCurrent = document.createElement("a");
    aCurrent.setAttribute("class", "page-link");
    aCurrent.innerHTML =
      this.currentDay + "/" + (this.currentMonth + 1) + "/" + this.currentYear;
    const aNext = document.createElement("a");
    aNext.setAttribute("class", "page-link");
    const nextDay = document.createElement("li");
    nextDay.setAttribute("class", "page-item");
    nextDay.setAttribute("type", "button");
    nextDay.setAttribute("id", "nextDay");
    aNext.innerHTML = ">";
    const aPrev = document.createElement("a");
    aPrev.setAttribute("class", "page-link");
    const prevDay = document.createElement("li");
    prevDay.setAttribute("class", "page-item");
    prevDay.setAttribute("type", "button");
    prevDay.setAttribute("id", "prevDay");
    aPrev.innerHTML = "<";

    prevDay.appendChild(aPrev);
    nextDay.appendChild(aNext);
    currentDay.appendChild(aCurrent);

    info_day.appendChild(prevDay);
    info_day.appendChild(currentDay);
    info_day.appendChild(nextDay);

    action_day.appendChild(info_day);
    this.initButtonsDay();
    const dayPage = document.createElement("div");
    let day = this.currentDay;
    let html = "";
    const dayCol = document.createElement("div");
    dayCol.setAttribute("class", "dayCol");
    switch (first_day_weekday) {
      case 1:
        html = "Lundi " + day + " " + this.monthsLabels[this.currentMonth];
        break;
      case 2:
        html = "Mardi " + day + " " + this.monthsLabels[this.currentMonth];
        break;

      case 3:
        html = "Mercredi " + day + " " + this.monthsLabels[this.currentMonth];
        break;

      case 4:
        html = "Jeudi " + day + " " + this.monthsLabels[this.currentMonth];
        break;

      case 5:
        html = "Vendredi " + day + " " + this.monthsLabels[this.currentMonth];
        break;

      case 6:
        html = "Samedi " + day + " " + this.monthsLabels[this.currentMonth];
        break;

      case 7:
        html = "Dimanche " + day + " " + this.monthsLabels[this.currentMonth];
        break;
      default:
        console.log("wtf");
        break;
    }
    dayCol.innerHTML = html;
    dayPage.appendChild(dayCol);

    for (let i = 8; i <= 20; i++) {
      const hourRow = document.createElement("div");
      hourRow.classList.add("hourDay");
      const hour = document.createElement("div");
      hour.setAttribute("class", "hour");
      const strHour = document.createElement("span");
      strHour.setAttribute("class", "strHour");
      strHour.innerHTML = i + "h";
      hour.appendChild(strHour);
      hourRow.appendChild(hour);
      dayPage.appendChild(hourRow);
    }
    dayDiv.appendChild(dayPage);
  }

  // Button for Day SHOW
  initButtonsDay() {
    document.getElementById("nextDay").addEventListener("click", () => {
      this.nav++;
      console.log(this.nav);
    });

    document.getElementById("prevDay").addEventListener("click", () => {
      this.nav--;
      console.log(this.nav);
    });
  }
}
