@extends('layouts.app')


<script src="https://maps.googleapis.com/maps/api/js?key={{ $GOOGLE_MAPS_API_KEY }}&libraries=places&callback="></script>
@section('content')



<?php
if(isset($GOOGLE_MAPS_API_KEY)) { ?>
    <school></school>
<?php
} else { ?>
<school-mapbox accesstoken="<?php echo $MAPBOX_API_KEY;?>"></school-mapbox>
<?php
}
?>

@endsection
