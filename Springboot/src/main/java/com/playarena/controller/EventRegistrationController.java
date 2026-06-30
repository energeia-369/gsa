package com.playarena.controller;

import com.playarena.entity.EventRegistration;
import com.playarena.service.EventRegistrationService;

import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/event-registrations")
@CrossOrigin(origins = "http://localhost:5173")
public class EventRegistrationController {

    private final EventRegistrationService service;

    public EventRegistrationController(
            EventRegistrationService service
    ) {
        this.service = service;
    }

    @PostMapping
    public EventRegistration registerEvent(
            @RequestBody EventRegistration registration
    ) {
        return service.registerEvent(registration);
    }

    @GetMapping
    public java.util.List<EventRegistration> getAllRegistrations() {
        return service.getAllRegistrations();
    }
}