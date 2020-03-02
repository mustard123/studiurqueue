import { Component, OnInit, ViewChild, ElementRef, AfterViewInit, HostListener, } from '@angular/core';
import { Router } from '@angular/router';

import { TicketService } from '../ticket.service'
import { Subject } from 'rxjs';
import { HasElementRef } from '@angular/material/core/typings/common-behaviors/color';
import { HttpClient } from '@angular/common/http';
import { Ticket } from '../interfaces/StudQNotification';
import { MatSnackBar } from '@angular/material/snack-bar';

import {
  trigger,
  state,
  style,
  animate,
  transition,
} from '@angular/animations';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss'],
  animations: [
    trigger('create_close', [
      state('void', style([
        {opacity: 0,
        transform: 'scale(0.5)'}
      ])),

      transition('void => *', [
        animate('1s')
      ]),

      transition('* => void', [
        style([{
          background: 'green'
        }]),
        animate(200)
      ])
    ])

  ]
})

export class HomeComponent implements OnInit, AfterViewInit {



  @ViewChild("button", { static: false }) startButton: HasElementRef

  state = {
    PROCESSING: false
  }


  currentTicketId:number = -1


  constructor(private router: Router,
    private ticketService: TicketService,
    private http: HttpClient,
    private snackBar: MatSnackBar
  ) { }


  tickets: Array<Ticket> = []

  dataReadySubj: Subject<any> = new Subject()



  ngOnInit() {

    console.log(this.state.PROCESSING, !this.state.PROCESSING)
    this.ticketService.getTickets().subscribe((tickets) => {
      this.tickets = tickets
      this.dataReadySubj.next('ready')
    }, (err) => {
      console.log(err)
    })

    this.ticketService.connectionObs.subscribe(info => {
      if (!info.success){
        let errorSnack = this.snackBar.open('Verbindungsfehler. Wird neu verbunden in 3s.', 'info', {
          duration: 3000
        })
      }else {
        let successSnack = this.snackBar.open('Vebunden', '✓', {
          duration: 3000
        })
      }
     
    })
  }



  start() {
    this.currentTicketId = this.tickets.find(t => t.status === 'open').id
    this.ticketService.startTicket(this.currentTicketId).then(()=>{
      this.state.PROCESSING = true
    }).catch(e => {
      console.log(e)
    })
  }

  stop(){
    this.ticketService.closeTicket(this.currentTicketId).then(()=>{
      this.state.PROCESSING = false
      this.currentTicketId = -1
      setTimeout(() => {
        this.startButton._elementRef.nativeElement.focus()
      }, 0);
    })
    
   
  }

  disabled (){
    if (this.tickets.length === 0 || this.state.PROCESSING) {
      return true
    }
    if (this.tickets.find(e => e.status === "open")){
      return false
    }
    else {
      return true
    }

  }

  trackByFn(index, item) {
    return item.id
  }

  ngAfterViewInit(): void {
    this.startButton._elementRef.nativeElement.focus()
    this.dataReadySubj.subscribe((d) => {
      setTimeout(() => {
        this.startButton._elementRef.nativeElement.focus()
      }, 0);

    })
  }

}
