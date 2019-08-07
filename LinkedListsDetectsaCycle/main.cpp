/*
Detect a cycle in a linked list. Note that the head pointer may be 'NULL' if the list is empty.
A Node is defined as: 
    struct Node {
        int data = 0;
        struct Node* next;
    }
*/

bool has_cycle(Node* head) {
    // Complete this function
    // Do not write the main method
    int i=0;
    if(head) i = head->data;
    while(head)
    {     
        if(head->data==i+1) return true;
        head->data=i+1;      
        head = head->next;
    }
    return false;
}