import { Component } from '@angular/core';
import {ViewChild} from '@angular/core';
import {MatSidenav} from '@angular/material/sidenav';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  @ViewChild('sidenav', {static: false}) sidenav: MatSidenav;
  
  close() {
    this.sidenav.close();
  } 
  title = 'studiur-queue';
}
