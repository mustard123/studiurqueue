export interface StudQNotification{
    header: {
        action: string

    },

    payload: Ticket
}

export type TicketStatus = 'open'|'closed'|'processing';

export interface Ticket {

    id: number
    date: string,
    status: TicketStatus,
    user?: User
}

export interface User {
    id: number,
    email: string,
    name?: string,
    surname?: string
}