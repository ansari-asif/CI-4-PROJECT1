<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card p-3">
                <form method="post" action="/login">
                    <p class="text-danger"> <?=$error??'';?></p>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" value="<?=$data['email']??''?>">
                        <span class="text-danger"><?=$errors['email']??'';?></span>
                        
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" value="<?=$data['password']??''?>">
                        <span class="text-danger"><?=$errors['password']??'';?></span>
                    </div>                    
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>