// src/controllers/planning_read_controller.js
import { Controller } from "@hotwired/stimulus";

import { Month } from "../js/class/Month";

export default class extends Controller {
  static targets = ["name", "output"];

  connect() {
    let calendar = new Month();
    calendar.read();
  }
}
