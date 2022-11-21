<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CodePen - Signature Pad - demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('signature/dist/signature-style.css') }}">

</head>

<body>
    <!-- partial:index.partial.html -->
    <section class="signature-component">
        <h1>Draw Signature</h1>
        <h2>with mouse or touch</h2>

        <canvas id="signature-pad" width="400" height="200"></canvas>

        <div>
          <button id="save">Save</button>
          <button id="clear">Clear</button>
          <button id="showPointsToggle">Show points?</button>
        </div>

        <p>
          <br />
          <a href="https://codepen.io/kunukn/pen/bgjzpb/" target="_blank">Throttling without lag example here</a><br />
          <br />
          <a href="https://github.com/szimek/signature_pad" target="_blank">Signature Pad</a> with custom Simplifying and Throttling
        </p>
      </section>
    <!-- partial -->
    <script src="{{ asset('signature/dist/underscore-min.js') }}"></script>
    <script src="{{ asset('signature/dist/signature-script.js') }}"></script>

</body>

</html>
