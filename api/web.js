var express = require("express");
var iniparser = require('iniparser');
var sanitizer = require("sanitizer");
var mysql      = require('mysql');
var config = iniparser.parseSync('../configs/db.ini');
var app = express();
app.use(express.logger());



var connection = mysql.createConnection({
  host     : config.mysql_host,
  user     : config.mysql_user,
  password : config.mysql_pass,
  database: config.mysql_db
});

connection.connect();

app.configure(function(){
  app.set('views', __dirname + '/views');
  app.set('view engine', 'jade');
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(app.router);
  app.use(express.static(__dirname + '/public'));
});

app.get('/bankrate',function(req,res){
	var sql = "select * from bank_rate where date = CURDATE()";
	var query = connection.query(sql, function(err, rows, fields) {
		res.send(JSON.stringify(rows));  
	});
	console.log(query.sql);
	
});


app.get('/bestrate/:amount_in_usd',function(req,res){
	var amount_in_usd = sanitizer.escape(req.params.amount_in_usd);
	var sql = "select provider,round(rate*"+amount_in_usd+"-india_fee*"+amount_in_usd+",2) as remittance_amount_in_inr,round("+amount_in_usd+"+flat_fee,2) as cost_in_usd, round((rate*"+amount_in_usd+"-india_fee*"+amount_in_usd+")/("+amount_in_usd+"+flat_fee),2) as effective_rate from exchange_rate_daily where date = CURDATE() and "+amount_in_usd+" between slab_start and slab_end order by effective_rate desc";
	var query = connection.query(sql, function(err, rows, fields) {
		res.send(JSON.stringify(rows));  
	});
	console.log(query.sql);
	
});

var port = 5000;
app.listen(port, function() {
  console.log("Listening on " + port);
});