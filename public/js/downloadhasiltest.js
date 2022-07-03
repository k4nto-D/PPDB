// $('.btn-print').on('click',function(event) {
//     // get size of report page
//     var reportPageHeight = $('#canvas').innerHeight();
//     var reportPageWidth = $('#canvas').innerWidth();

//     // create a new canvas object that we will populate with all other canvas objects
//     var pdfCanvas = $('<canvas />').attr({
//       id: "canvaspdf",
//       width: reportPageWidth,
//       height: reportPageHeight
//     });

//     // keep track canvas position
//     var pdfctx = $(pdfCanvas)[0].getContext('2d');
//     var pdfctxX = 0;
//     var pdfctxY = 0;
//     var buffer = 1000;

//     // for each chart.js chart
//     $("canvas").each(function(index) {
//       // get the chart height/width
//       var canvasHeight = $(this).innerHeight();
//       var canvasWidth = $(this).innerWidth();

//       // draw the chart into the new canvas
//       pdfctx.drawImage($(this)[0], pdfctxX, pdfctxY, canvasWidth, canvasHeight);
//       pdfctxX += canvasWidth + buffer;

//       // our report page is in a grid pattern so replicate that in the new canvas
//       if (index % 2 === 1) {
//         pdfctxX = 0;
//         pdfctxY += canvasHeight + buffer;
//       }
//     });

//     // create new pdf and add our new canvas as an image
//     var pdf = new jsPDF('l');
//     pdf.addImage($(pdfCanvas)[0], 'PNG', 0, 10);
//     var now = new Date();
//     var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();
//     pdf.text(130, 10, "Rekap Laporan");
//     pdf.setFontSize(8);
//     pdf.text(10, 200, "Date printed - " + jsDate);
//     // download the pdf
//     pdf.save('Rekap Laporan - '+ jsDate +'.pdf');
//   });


//! WARNING 
// var doc = new jsPDF();
//       var specialElementHandlers = {
//       '#editor': function (element, renderer) {
//             return true;
//       }
//       };
//       $('.btn-print').click(function () {
//       doc.fromHTML($('#canvas').html(), 15, 15, {
//             'width': 170,
//             'elementHandlers': specialElementHandlers
//       });
//             doc.save('download.pdf');
//       });

// $('.btn-print').on('click',function(event) {
//       var doc = new jsPDF();
//       var elementHandler = {
//             '#canvas': function (element, rendered) {
//                   return true;
//             }
//       };

//       var source = window.document.getElementsByTagName('body')[0];
//       doc.fromHTML(
//             source,
//             25,
//             25,
//             {
//                   'width': 180,'elementHandlers': elementHandler
//             }
//       );

//       window.open(doc.output('bloburl'))

// });



$('.btn-print').on('click',function(event) {
      
            addHTMLToPDFPage();
      

});

function addHTMLToPDFPage() {

	var doc = new jsPDF('p', 'pt', [$("#canvas").innerWidth(), $("#canvas").innerHeight()]);
	
	converHTMLToCanvas($("#canvas")[0], doc, false, true);
}

function converHTMLToCanvas(element, jspdf, enableAddPage, enableSave) {
	html2canvas(element, { allowTaint: true }).then(function(canvas) {
		if(enableAddPage == true) {
			jspdf.addPage($("#canvas").innerWidth(), $("#canvas").innerHeight());
		}
		
		image = canvas.toDataURL('image/png', 2.0);
		jspdf.addImage(image, 'PNG', 200, 200, $(element).innerWidth(), $(element).innerHeight()); // A4 sizes
		
		if(enableSave == true) {
			jspdf.save("add-to-multi-page.pdf");
		}
	});
            // var reportPageHeight = $('#canvas').innerHeight();
            // var reportPageWidth = $('#canvas').innerWidth();
}
