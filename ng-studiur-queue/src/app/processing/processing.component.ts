import { Component, OnInit, ViewChild, ElementRef, AfterViewInit, HostListener, OnDestroy, Input, Output, EventEmitter } from '@angular/core';
import { TicketService } from '../ticket.service'
import { HasElementRef } from '@angular/material/core/typings/common-behaviors/color';
import { Ticket } from '../interfaces/StudQNotification';
import { SwalComponent } from '@sweetalert2/ngx-sweetalert2';




@Component({
  selector: 'app-processing',
  templateUrl: './processing.component.html',
  styleUrls: ['./processing.component.scss']
})



export class ProcessingComponent implements OnInit, AfterViewInit, OnDestroy {


  @HostListener('window:beforeunload', ['$event'])
  unsavedChanges($event){
    setTimeout(() => {
      this.swal.fire()
    }, 0);
    return false
  }

  @Input() ticketId: number

  @ViewChild('promptDataLoss', {static: false}) swal: SwalComponent

  @ViewChild("button", { static: false }) closeButton: HasElementRef
  @Output() closeTicket = new EventEmitter();


  ticket: Ticket

  timeElapsed = 0

  interval
  
  constructor(
    private ticketService: TicketService) { }

  ngOnInit() {

    let startTime = new Date().getTime()
    this.interval = setInterval(()=>{
      this.timeElapsed = new Date().getTime() - startTime
      console.log(this.timeElapsed)
    }, 1000);

    this.ticket = this.ticketService.getTicketById(this.ticketId)
    console.log(this.ticket)
    let fullname = this.ticket.user.email.match(/.+?(?=@)/)[0]
    this.ticket.user.name = fullname.split('.')[0]
    this.ticket.user.surname = fullname.split('.')[1]



  }

  stop() {
    this.closeTicket.emit(this.ticket.id)
  }

  ngAfterViewInit(): void {
    this.closeButton._elementRef.nativeElement.focus()
  }

  ngOnDestroy(): void {
   clearInterval(this.interval)
  }


}
