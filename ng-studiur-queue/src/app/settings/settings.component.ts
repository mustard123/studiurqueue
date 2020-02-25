import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';


@Component({
  selector: 'app-settings',
  templateUrl: './settings.component.html',
  styleUrls: ['./settings.component.scss']
})
export class SettingsComponent implements OnInit {

  constructor(private http: HttpClient) { }

  error: boolean = false
  errorMessage: string = ""
  makeAdminsList = ""
  removeAdminsList = ""
  currentAdmins = ""

  ngOnInit() {
    this.http.put("/backend/api/admin-settings", {}, {
      headers: {
        Authorization: localStorage.getItem('jwtToken')
      }
    }).toPromise().then((res: any) => {
      this.currentAdmins = res.currentAdmins
    }).catch(e => {
      this.currentAdmins = e.message
    })
  }

  submit() {
    this.error = false
    this.errorMessage = ""

    this.makeAdminsList = this.makeAdminsList.replace(/(^[,\s]+)|([,\s]+$)/g, '')
    this.removeAdminsList = this.removeAdminsList.replace(/(^[,\s]+)|([,\s]+$)/g, '')
    let pattern = /[^\s]/g;
    let addAdmins
    let removeAdmins
    try {
      addAdmins = this.makeAdminsList.match(pattern).join("").split(",")
    }
    catch (e) {
      addAdmins = []
    }
    try {
      removeAdmins = this.removeAdminsList.match(pattern).join("").split(",")
    }
    catch (e) {
      removeAdmins = []
    }

    let request: any = {}
    request.add = addAdmins
    request.remove = removeAdmins

    console.log(request)

    this.http.put("/backend/api/admin-settings", request, {
      headers: {
        Authorization: localStorage.getItem('jwtToken')
      }
    }).toPromise().then((res: any) => {
      this.currentAdmins = res.currentAdmins
      console.log(this.currentAdmins)
    }).catch((e: any) => {
      this.errorMessage = e.message
      this.error = true
    })

    this.makeAdminsList = ""
    this.removeAdminsList = ""

  }

}
