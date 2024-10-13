@include('scripts')
@include('admin.adminNavbar')

<!-- Title -->
<div class="title">Create News Event</div>

<!-- Wrapper to center the form -->
<div class="wrapper">
    <!-- Flip card structure -->
    <div class="flip-card__inner">
        <!-- Front card (create event) -->
        <div class="flip-card__front">
            <form action="{{ route('news-events.store') }}" method="POST" enctype="multipart/form-data" class="flip-card__form">
                @csrf
                <!-- Title Field -->
                <input type="text" name="title" class="flip-card__input" placeholder="Title" required>

                <!-- Description Field -->
                <textarea name="description" class="flip-card__input" placeholder="Description" required></textarea>

                <!-- Event Date Field -->
                <input type="date" name="event_date" class="flip-card__input" placeholder="Event Date" required>

                <!-- Event Image Field -->
                <input type="file" name="eventimage" class="flip-card__input" placeholder="Event Image">

                <!-- Submit Button -->
                <button type="submit" class="flip-card__btn">Create</button>
            </form>
        </div>
    </div>
</div>

<style>
/* Centering the wrapper */
.wrapper {
  display: flex;
  justify-content: center;
  align-items: flex-start; /* Align items at the start */
  height: 100vh; /* Full viewport height */
  background-color: #f0f0f0; /* Light background to distinguish the card */
}

/* card structure */
.flip-card__inner {
  width: 450px; /* Increased width for the card */
  height: 520px; /* Keeping height the same */
  position: relative;
  background-color: transparent;
  perspective: 1000px;
  text-align: center;
  transition: transform 0.8s;
  transform-style: preserve-3d;
}

.flip-card__front, .flip-card__back {
  padding: 32px; /* Increased padding */
  position: absolute;
  display: flex;
  flex-direction: column;
  justify-content: center;
  backface-visibility: hidden;
  background: lightgrey;
  gap: 20px;
  border-radius: 5px;
  box-shadow: 4px 4px var(--main-color);
}

.flip-card__back {
  transform: rotateY(180deg);
}

/* form styling */
.flip-card__form {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
  border: 2px solid var(--main-color); /* Adding border to the form */
  padding: 32px; /* Increased padding inside the form */
  border-radius: 5px; /* Rounded corners for the form border */
  background-color: #fff; /* Background color for the form */
  margin-top: 10px; /* Adjust margin to move the form further up */
}

.title {
  margin: 20px 0;
  font-size: 39px; /* Increased font size */
  font-weight: 900;
  text-align: center;
  color: var(--main-color);
}

.flip-card__input {
  width: 350px; /* Increased width for the input fields */
  height: 52px; /* Keeping height the same */
  border-radius: 5px;
  border: 2px solid var(--main-color);
  background-color: var(--bg-color);
  box-shadow: 4px 4px var(--main-color);
  font-size: 20px; /* Increased font size */
  font-weight: 600;
  color: var(--font-color);
  padding: 8px; /* Adjusted padding */
  outline: none;
}

.flip-card__input::placeholder {
  color: var(--font-color-sub);
  opacity: 0.8;
}

.flip-card__input:focus {
  border: 2px solid var(--input-focus);
}

/* Button with border */
.flip-card__btn {
  margin: 20px 0;
  width: 156px; /* Keeping width the same */
  height: 52px; /* Keeping height the same */
  border-radius: 5px;
  border: 2px solid var(--main-color); /* Adding border to the button */
  background-color: var(--bg-color);
  box-shadow: 4px 4px var(--main-color);
  font-size: 20px; /* Keeping font size the same */
  font-weight: 600;
  color: var(--font-color);
  cursor: pointer;
}

.flip-card__btn:active {
  box-shadow: 0px 0px var(--main-color);
  transform: translate(3px, 3px);
}
</style>
