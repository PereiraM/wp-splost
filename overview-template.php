<?php
/*
Template Name: Overview
*/
?>

<?php get_header(); ?>
 <div id="maincontainer" class="overview" >
   <h3>Overview Description</h3>
   <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

   					<?php if ( is_front_page() ) { ?>
   						<!-- ><h2><?php the_title(); ?></h2> -->
   					<?php } else { ?>	
   					<!--	<h1><?php the_title(); ?></h1> -->
   					<?php } ?>				

   						<?php the_content(); ?>

  <h3>Economic Development Quick Stats</h3>
    <div id="stats"></div>
  <h3>Project Locations</h3>
    <div id="map" class="fullmap"></div>    
  <h3>Category Funding Comparison</h3>
    <p>Below, a funds comparison between this category's projects.</p>
	  <div id="holderEd"></div>
  <h3>Project Funding Schedule</h3>
    <p>This is the funding schedule budget as proposed by the SPLOST bill.<p>
    <div id="schedule"></div>
  <h3>Economic Development Monthly Revenue</h3>
    <p>Each month we publish a report on our expenses and tax/bond revenue. Below is an itemization for Economic Development related expenses. You can find an archive of reports <a href="http://splost.codeforamerica.org/?s=monthly+report">here</a>.</p>
    <div id="monthly"></div>

  <span class="button wpedit">
    <?php edit_post_link( __( 'Edit', 'twentyten' ), '', '' ); ?></span>
    <?php comments_template( '', true ); ?>

   <?php endwhile; ?>
</div><!-- end #maincontainer -->
    
    
    <script id="stats" type="text/html">
       <p><span class="statHighlight">{{numberActive}} of {{numberTotalProjects}}</span> projects are active</p>
       <p><span class="statHighlight">{{numberCompletedProjects}}</span> projects are completed</p>
       <p><span class="statHighlight">{{totalSpent}}</span> has been spent as of {{currentDate}} </p>
     </script>
    
   <script id="schedule" type="text/html">
      <table>
      <thead>
      <tr class="tableheader">
      <th>PROJECT</th><th>TOTAL</th><th>2012</th><th>2013</th><th>2014</th><th>2015</th><th>2016</th><th>2017</th><th>2018</th><th>2019</th>
      </tr>
      </thead>
      {{#rows}}
        <tr><td class = "project">{{project}}</td><td class="total">{{total}}</td><td class="yrdolls">{{year2012}}</td><td class="yrdolls">{{year2013}}</td><td class="yrdolls">{{year2014}}</td><td class="yrdolls">{{year2015}}</td><td class="yrdolls">{{year2016}}</td><td class="yrdolls">{{year2017}}</td><td class="yrdolls">{{year2018}}</td><td class="yrdolls">{{year2019}}</td></tr>
      {{/rows}}
      </table>
    </script>

  <script id="monthly" type="text/html">
      <h6 class="fleft">Monthly Report for:</h6> 
      <p><span class="statHighlight">  {{reportmonth}} {{reportyear}}</span></p>
      <table class="monthlytable">
      <thead>
      <tr class="tableheader">
      <th>PROJECT</th><th>SUB PROJECT</th><th>ITEM</th><th>Budget</th><th>Actual</th>
      </tr>
      </thead>
      {{#rows}}
        <tr>
        <td >{{project}}</td><td>{{subproject}}</td><td >{{item}}</td><td class="tright">{{budgeted}}</td><td class="tright">{{actual}}</td></tr>
      {{/rows}}
      </table>
    </script>
    
    
    <script type="text/javascript">    
      document.addEventListener('DOMContentLoaded', function() {
         loadSpreadsheet(showInfo)
       })    

       function showInfo(data, tabletop) {
               
               
         accounting.settings.currency.precision = 0

         var edProjects = getType(tabletop.sheets("projections").all(), "Economic Development")
         var drProjects = getType(data, "Debt Retirement")
         var raProjects = getType(data, "Rec & Cultural Arts")
         var psProjects = getType(data, "Public Safety")

         var map = loadMap()
         edProjects.forEach(function (edProject){
           displayAddress(map, edProject)
         })

         function pushBits(element) {
            values.push(parseInt(element.total))
            labels.push(element.project)
            hexcolors.push(element.hexcolor)
          }
              
          var r = Raphael("holderEd")
          var values = []
          var labels = []
          var hexcolors = []
              edProjects.forEach(pushBits)

      // (paper, x, y, width, height, values, opts)
      r.g.hbarchart(170, 15, 480, 90, values, {stacked: true, type: "soft", colors: hexcolors, gutter: "20%"}).hoverColumn(
        function() { 
          var y = []
          var res = []

              for (var i = this.bars.length; i--;) {
                  y.push(this.bars[i].y);
                  res.push(this.bars[i].value || "0");
              }
              this.flag = r.g.popup(this.bars[0].x, Math.min.apply(Math, y), res.join(", ")).insertBefore(this);
      }, function() {
            this.flag.animate({opacity: 0}, 1500, ">", function () {this.remove();});
      });
      // (x, y, length, from, to, steps, orientation, labels, type, dashsize, paper)
      axis = r.g.axis(160,80,45,null, null,1,1, labels.reverse(), null, 1);
      axis.text.attr({font:"12px Arvo", "font-weight": "regular", "fill": "#333333"}); 
          
              
               
          
          
          
         var numberActive = getActiveProjects(edProjects).length
         var numberTotalProjects = 2
         var numberCompletedProjects = completedProjects(edProjects)
         var totalSpent = amountSpent(edProjects)

         var monthlyrev = tabletop.sheets("revenue").all()
         var reportmonth = "August"
         var reportyear = 2012


// turnCurrency(edProjects)
         var schedule = ich.schedule({
          "rows": turnCurrency(edProjects)
         })

         var monthly = ich.monthly({
          "rows": turnMonthlyCurrency(monthlyrev),
          "reportyear": reportyear,
          "reportmonth": reportmonth
               })

         var stats = ich.stats({
           "numberActive": numberActive,
           "numberTotalProjects": numberTotalProjects,
           "numberCompletedProjects": numberCompletedProjects,
           "totalSpent": accounting.formatMoney(totalSpent),
           "currentDate": getCurrentYear()
         })

         document.getElementById('schedule').innerHTML = schedule;
         document.getElementById('stats').innerHTML = stats; 
         document.getElementById('monthly').innerHTML = monthly; 

       }
    </script>



<?php get_sidebar(); ?>
<?php get_footer(); ?>