import { Injectable } from '@angular/core';
import { Observable, of, Subject, ReplaySubject } from 'rxjs';
import { StudQNotification, Ticket, TicketStatus } from './interfaces/StudQNotification';
import { HttpClient } from '@angular/common/http';


@Injectable({
  providedIn: 'root'
})
export class TicketService {

  replaySubject: ReplaySubject<Array<Ticket>> = new ReplaySubject(1);
  private connectionSubject: Subject<any> = new ReplaySubject(1)
  public connectionObs = this.connectionSubject.asObservable()
  tickets: Array<Ticket> = []

  authHeader = {
    Authorization: localStorage.getItem('jwtToken')
  }

  constructor(private http: HttpClient) {
    this.connectSocket()
  }


  getTickets(): Observable<Array<Ticket>> {
    return this.replaySubject.asObservable()
  }

  closeTicket(id: number) {
    return this.http.put(`/backend/api/ticket/${id}/close`, {}, { headers: this.authHeader }).toPromise()

  }

  startTicket(id: number) {
    return this.http.put(`/backend/api/ticket/${id}/start`, {}, { headers: this.authHeader }).toPromise()

  }

  getTicketById(id: number) {
    return this.tickets.find(e => e.id == id)

  }

  private connectSocket() {

    const socket = new WebSocket(`ws://localhost:7777?token=${localStorage.getItem('jwtToken')}`)

    socket.onopen = () => {
      this.connectionSubject.next({success: true})
      this.http.get('/backend/api/tickets', {
        headers: this.authHeader
      }).toPromise().then((tickets: Array<Ticket>) => {
        console.log(tickets)
        this.tickets = tickets
        this.replaySubject.next(this.tickets)
      })
    }

    socket.onmessage = (event) => {
      const notification = JSON.parse(event.data)
      console.log(notification)
      switch (notification.header.action) {
        case 'CREATE':{
          this.tickets.push(notification.payload)
          break}
        case 'DELETE': {
          let index = this.tickets.findIndex((e) => e.id === notification.payload.id)
          this.tickets.splice(index, 1)
          break}
        case 'CLOSE':{
          let index = this.tickets.findIndex((e) => e.id === notification.payload.id)
          if (index >=0)
          this.tickets.splice(index, 1)
          break}
        case 'START':{
          let ticket = this.tickets.find((e) => e.id === notification.payload.id)
          if(ticket)
          ticket.status = 'processing'
          break}
        default:{
          console.log('unsupported action:', notification.header.action)
          this.replaySubject.next(this.tickets)}
      }
    }

    socket.onclose = (event) => {
      this.connectionSubject.next({success: false, reason: event.reason})
      console.log('Socket is closed. Reconnect will be attempted in 3 second.', event.reason);
      setTimeout(() => {
        this.connectSocket();
      }, 3000);
    }

    socket.onerror = (err) => {
      console.error('Socket encountered error: ', err , 'Closing socket');
      socket.close();
    }

  }



}




