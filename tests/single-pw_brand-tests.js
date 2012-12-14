asyncTest("Persona", function() {
  expect(4);
  PERSONA.update(function() {
    ok(PERSONA.getId(), "A person ID has been fetched");
    ok(PERSONA.getName(), "The person is called " + PERSONA.getName());
    ok(PERSONA.getImage(), "There's a photo of the person");
    strictEqual(PERSONA.getUpdates().length, 3, "The person has three updates");
    start();
  });
});

asyncTest("Inspiration", function() {
  expect(5);
  PERSONA.update(function() {
    INSPIRATION.update(function() {
      ok(INSPIRATION.getSayWord(),    "Has say word: "    + INSPIRATION.getSayWord());
      ok(INSPIRATION.getDoWord(),     "Has do word: "     + INSPIRATION.getDoWord());
      ok(INSPIRATION.getThinkWord(),  "Has think word: "  + INSPIRATION.getThinkWord());
      ok(INSPIRATION.getOwnWord(),    "Has own word: "    + INSPIRATION.getOwnWord());
      ok(INSPIRATION.getInspirationImage(), "Has inspiration image");
      start();
    });
  });
});