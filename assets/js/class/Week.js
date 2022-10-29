// currentDate = new Date();
// startDate = new Date(currentDate.getFullYear(), 0, 1);
// var days = Math.floor((currentDate - startDate) /
//     (24 * 60 * 60 * 1000));

// var weekNumber = Math.ceil(days / 7);

// // Display the calculated result
// document.write("Week number of " + currentDate +
//     " is :   " + weekNumber);

export class Week {
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
    const action_week = document.getElementById("action-week");
    const week = document.getElementById("week");
    action_week.innerHTML = "";
    week.innerHTML = "";
    // Initialise la date
    this.currentDate = new Date();
    this.currentYear = this.currentDate.getFullYear();
    let first_day_weekday =
      this.currentDate.getDay() == 0 ? 7 : this.currentDate.getDay();
    this.currentDay = this.currentDate.getDate();
    // Current Month
    this.currentMonth = this.currentDate.getMonth();
    this.firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1);
    this.daysInMonth = new Date(
      this.currentYear,
      this.currentMonth + 1,
      0
    ).getDate();
    this.previous_month_length = new Date(this.year, this.month, 0).getDate();

    const info_week = document.createElement("ul");
    info_week.setAttribute("class", "pagination");
    info_week.setAttribute("id", "info-week");
    const currentWeek = document.createElement("li");
    currentWeek.setAttribute("class", "page-item");
    currentWeek.setAttribute("id", "currentMonth");
    const aCurrent = document.createElement("a");
    aCurrent.setAttribute("class", "page-link");
    aCurrent.innerHTML =
      (first_day_weekday == 1
        ? this.currentDay + "/" + (this.currentMonth + 1)
        : monday + "/" + (this.currentMonth + 1)) +
      " - " +
      (first_day_weekday == 1
        ? this.currentDay + 6 + "/" + (this.currentMonth + 1)
        : monday + 7 + "/" + (this.currentMonth + 1));
    const aNext = document.createElement("a");
    aNext.setAttribute("class", "page-link");
    const nextWeek = document.createElement("li");
    nextWeek.setAttribute("class", "page-item");
    nextWeek.setAttribute("type", "button");
    nextWeek.setAttribute("id", "nextWeek");
    aNext.innerHTML = ">";
    const aPrev = document.createElement("a");
    aPrev.setAttribute("class", "page-link");
    const prevWeek = document.createElement("li");
    prevWeek.setAttribute("class", "page-item");
    prevWeek.setAttribute("type", "button");
    prevWeek.setAttribute("id", "prevWeek");
    aPrev.innerHTML = "<";

    prevWeek.appendChild(aPrev);
    nextWeek.appendChild(aNext);
    currentWeek.appendChild(aCurrent);

    info_week.appendChild(prevWeek);
    info_week.appendChild(currentWeek);
    info_week.appendChild(nextWeek);

    action_week.appendChild(info_week);

    this.initButtonsWeek();

    // Affichage

    let day = this.currentDay;
    for (let i = 1; i <= 7; i++) {
      const dayRectangle = document.createElement("div");
      if (i < first_day_weekday) {
        day = this.currentDay - first_day_weekday;
      }
      dayRectangle.classList.add("dayWeek");
      const dayCol = document.createElement("div");
      dayCol.setAttribute("class", "dayCol");
      let html = "";
      switch (i) {
        case 1:
          html = "Lundi " + day + "/" + (this.currentMonth + 1);
          break;
        case 2:
          html = "Mardi " + day + "/" + (this.currentMonth + 1);
          break;

        case 3:
          html = "Mercredi " + day + "/" + (this.currentMonth + 1);
          break;

        case 4:
          html = "Jeudi " + day + "/" + (this.currentMonth + 1);
          break;

        case 5:
          html = "Vendredi " + day + "/" + (this.currentMonth + 1);
          break;

        case 6:
          html = "Samedi " + day + "/" + (this.currentMonth + 1);
          break;

        case 7:
          html = "Dimanche " + day + "/" + (this.currentMonth + 1);
          break;
        default:
          console.log("wtf");
          break;
      }
      dayCol.innerHTML = html;
      dayRectangle.appendChild(dayCol);

      for (let i = 8; i <= 20; i++) {
        const hour = document.createElement("div");
        hour.classList.add("hourWeek");
        hour.innerHTML = i + "h";
        dayRectangle.appendChild(hour);
      }
      day++;

      week.appendChild(dayRectangle);
    }
  }

  // Button for WEEK SHOW
  initButtonsWeek() {
    document.getElementById("nextWeek").addEventListener("click", () => {
      this.nav++;
      console.log(this.nav);
      this.load();
    });

    document.getElementById("prevWeek").addEventListener("click", () => {
      this.nav--;
      console.log(this.nav);
      this.load();
    });
  }

  _getMonday(date) {
    date = new Date(date);
    var day = date.getDay(),
      diff = date.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is sunday
    return new Date(new Date(date.setDate(diff)).setHours(0, 0, 0, 0));
  }

  _getSunday(date) {
    date = new Date(date);
    var day = date.getDay(),
      diff = date.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is sunday
    return new Date(new Date(date.setDate(diff + 6)).setHours(23, 59, 59, 999));
  }
}
