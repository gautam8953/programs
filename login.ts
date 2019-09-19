import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { JarwisService } from 'src/app/services/jarwis.service';
import { TokenService } from 'src/app/services/token.service';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  loginValidate: FormGroup;
  submitted = false;

  public form = {
    username:null,
    password:null
  };
  public error=null;

  constructor(
    private Jarwis:JarwisService,
    private Token:TokenService,
    private router: Router,
    private Auth:AuthService,
    private formBuilder: FormBuilder
    ) { }

    ngOnInit() {
      this.loginValidate = this.formBuilder.group({
        username: ['', [Validators.required]],
        password: ['', [Validators.required, Validators.minLength(6)]]
      });
    }

    get formControls() { return this.loginValidate.controls; }

  onSubmit(){
    this.loginValidate.value
    this.submitted = true;
    if (this.loginValidate.invalid) {
        return;
    }
    
    this.Jarwis.login(this.form).subscribe(
      data=>this.handleResponse(data),
      error=>this.handleErrors(error)
    );
  }

  handleResponse(data){
    this.Token.handle(data.access_token);
    localStorage.setItem('userID',data.userID);
    localStorage.setItem('entityID',data.entityID);
    this.Auth.changeAuthStatus(true);
    this.router.navigateByUrl('/company');
  }

  handleErrors(error){
    this.error=error.error.error;
  }

}
