export class Month {
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

  // METHOD

  load() {
    // Get element for inject
    const action_schedule = document.getElementById("action-schedule");
    const schedule = document.getElementById("schedule");
    action_schedule.innerHTML = "";
    schedule.innerHTML = "";
    // Initialise la date
    this.currentDate = new Date();
    this.currentDay = this.currentDate.getDate();
    this.currentYear = this.currentDate.getFullYear();

    if (this.nav !== 0) {
      this.currentMonth = this.currentDate.getMonth() + this.nav;
      if (this.currentMonth > 11) {
        this.currentYear =
          this.currentYear + Math.floor(this.currentMonth / 12);
        this.currentMonth = Math.floor(this.currentMonth % 12);
      } else if (this.currentMonth < 0) {
        console.log(this.currentMonth);
        console.log(Math.floor(this.currentMonth / 12));
        this.currentYear =
          this.currentYear + Math.floor(this.currentMonth / 12);
        this.currentMonth = Math.floor(this.currentMonth % -12) + 12;
        console.log(this.currentMonth);
        if (this.currentMonth === 12) {
          this.currentMonth = 0;
          console.log(this.currentMonth);
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

    // get first day of month and first week day
    let first_day_weekday =
      this.firstDayOfMonth.getDay() == 0 ? 7 : this.firstDayOfMonth.getDay();

    this.previous_month_length = new Date(this.year, this.month, 0).getDate();

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

    prevMonth.appendChild(aPrev);
    nextMonth.appendChild(aNext);
    currentMonth.appendChild(aCurrent);

    info_schedule.appendChild(prevMonth);
    info_schedule.appendChild(currentMonth);
    info_schedule.appendChild(nextMonth);

    action_schedule.appendChild(info_schedule);

    this.initButtons();
    let scroll = document.createElement("div");
    scroll.setAttribute("id", "scroll");
    scroll.setAttribute("class", "overflow-scroll");
    for (let i = 0; i <= 6; i++) {
      const dayLabel = document.createElement("div");
      dayLabel.classList.add("dayLabel");
      dayLabel.innerText = this.daysLabels[i];
      scroll.appendChild(dayLabel);
    }

    // Variable
    let prev = 1;
    let day = 1;
    let next = 1;

    for (let i = 1; i <= 42; i++) {
      const daySquare = document.createElement("div");

      if (i < first_day_weekday) {
        daySquare.classList.add("dayOtherMonth");
        daySquare.innerText =
          this.previous_month_length - first_day_weekday + 1 + prev;
        scroll.appendChild(daySquare);
        prev++;
      } else if (day <= this.daysInMonth) {
        if (
          day == this.currentDay &&
          this.currentMonth == this.currentDate.getMonth() &&
          this.currentYear == this.currentDate.getFullYear()
        ) {
          daySquare.classList.add("today");
        }
        daySquare.classList.add("day");
        daySquare.setAttribute("data", day);
        daySquare.innerText = day;
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
    this.initEvent();
  }

  // Init button for Month
  initButtons() {
    document.getElementById("nextButton").addEventListener("click", () => {
      this.nav++;
      this.load();
    });

    document.getElementById("prevButton").addEventListener("click", () => {
      this.nav--;
      this.load();
    });
  }

  initEvent() {
    let days = document.querySelectorAll("div.day");
    console.log(days);
    days.forEach((day) => {
      console.log(day);
      day.addEventListener("click", (event) => {
        console.log(event.target);
        let dayT = event.target.getAttribute("data");
        console.log(
          "vous souhaitez programmer une intervention le " +
            dayT +
            " " +
            this.monthsLabels[this.currentMonth] +
            this.currentYear
        );
      });
    });
  }
}
