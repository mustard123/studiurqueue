import { Component, OnInit, AfterViewInit, ViewChild } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { SwalComponent } from '@sweetalert2/ngx-sweetalert2';


@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit, AfterViewInit {

  @ViewChild('promptLoginSwal', {static: false}) swal: SwalComponent

  public canceled: boolean = false
  public failed: boolean = false
  public statusText: string = "Not logged in"

  constructor(private http: HttpClient, private router: Router, public dialog: MatDialog) { }

  ngOnInit() {
    localStorage.clear()
  }


  ngAfterViewInit(): void {    
    this.swal.fire()
  }

  openLoginSite(){
    window.open('/backend/login/login-page', '_blank', 'location=yes,scrollbars=yes,height=570,width=520,status=yes')
    this.checkLogin()
  }

  checkLogin(){
    this.statusText = "Checking for login"
    let intervalId = setInterval(() => {
      let token = localStorage.getItem("jwtToken")
      if (token && token.trim() !== ''){
        clearInterval(intervalId)

        if (this.parseJwt(token).userlevel <= 0) {
          this.failed = true
          this.statusText = "Not logged in"
          localStorage.setItem('badToken', token)
          localStorage.removeItem("jwtToken")
          return
        }
        this.statusText = "Login successful"
        this.router.navigate(["/home"])
      }

    }, 1000);
    
  }

  parseJwt (token) {
    try {
      return JSON.parse(atob(token.split('.')[1]));
    } catch (e) {
      console.log(e)
      return null;
    }
  };

}
