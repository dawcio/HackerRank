#include <stack>
#include <string>
#include <cstdio>
#include <iostream>


using namespace std;

bool is_balanced(string expression) {
    stack<char> bracketStack;
    int length = expression.length();   
    int i = 0;
    int open = 0;
    if (length % 2 != 0) return false;
    while (i < length)
    {      
        char bracket = expression[i];
        if (bracket == '{' || bracket == '[' || bracket == '(')
        {
            bracketStack.push(bracket);
            open++;
            if (open > length/2) return false;
        }
        else
        {
            if (bracketStack.empty() == true) return false;
            char topBracket = bracketStack.top();
            switch(bracket)
            {
                case '}':
                if(topBracket != '{') return false;
                else bracketStack.pop();
                break;
                case ']':
                if(topBracket != '[') return false;
                else bracketStack.pop();
                break;
                case ')':
                if(topBracket != '(') return false;
                else bracketStack.pop();
                break;
            }
        }
        i++;
    }
    return true;
}

int main(){
    int t;
    cin >> t;
    for(int a0 = 0; a0 < t; a0++){
        string expression;
        cin >> expression;
        bool answer = is_balanced(expression);
        if(answer)
            cout << "YES\n";
        else cout << "NO\n";
    }
    return 0;
}