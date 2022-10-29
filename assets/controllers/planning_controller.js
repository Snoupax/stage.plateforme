// src/controllers/planning_controller.js
import { Controller } from "@hotwired/stimulus";

import { Day } from "../js/class/Day";
import { Month } from "../js/class/Month";
import { Week } from "../js/class/Week";

export default class extends Controller {
  static targets = ["name", "output"];

  connect() {
    let calendar = new Month();
    calendar.load();
    // let week = new Week();
    // week.load();
    // let day = new Day();
    // day.load();
  }

  display() {
    console.log(`Hello`, this.element);
  }
}
