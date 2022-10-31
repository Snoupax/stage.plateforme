// src/controllers/testholding_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect() {
    // This timeout, started on mousedown, triggers the beginning of a hold
    var holdStarter = null;

    // This is how many milliseconds to wait before recognizing a hold
    var holdDelay = 500;

    // This flag indicates the user is currently holding the mouse down
    var holdActive = false;

    // MouseDown
    let spans = document.querySelectorAll("li.test");
    let status = document.querySelector(".status");
    console.log(spans);
    spans.forEach((span) => {
      span.addEventListener("mousedown", (e) => {
        onMouseDown(e);
      });

      span.addEventListener("mouseup", (e) => {
        onMouseUp(e);
      });
    });
    // Mouse Down
    function onMouseDown(e) {
      console.log(e.target.id);
      holdStarter = setTimeout(function () {
        holdStarter = null;
        holdActive = true;
        // begin hold-only operation here, if desired
        status.textContent = "holding...";
      }, holdDelay);
    }
    // MouseUp

    function onMouseUp(e) {
      // If the mouse is released immediately (i.e., a click), before the
      console.log(e.target.id);
      //  holdStarter runs, then cancel the holdStarter and do the click
      if (holdStarter) {
        clearTimeout(holdStarter);
        // run click-only operation here

        status.textContent = "Clicked!";
      }
      // Otherwise, if the mouse was being held, end the hold
      else if (holdActive) {
        holdActive = false;
        // end hold-only operation here, if desired

        status.textContent = "Holding same";
      }
    }

    // OnClick
    // not using onclick at all - onmousedown and onmouseup take care of everything

    // Optional add-on: if mouse moves out, then release hold
  }
}
