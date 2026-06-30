package com.playarena.service;

import com.playarena.entity.EventRegistration;
import com.playarena.repository.EventRegistrationRepository;
import org.springframework.stereotype.Service;

@Service
public class EventRegistrationService {

    private final EventRegistrationRepository repository;

    public EventRegistrationService(EventRegistrationRepository repository) {
        this.repository = repository;
    }

    public EventRegistration registerEvent(EventRegistration registration) {
        if (registration.getPaymentStatus() == null || registration.getPaymentStatus().isEmpty()) {
            registration.setPaymentStatus("PENDING");
        }
        if (registration.getRegistrationFee() == 0) {
            registration.setRegistrationFee(2499);
        }
        return repository.save(registration);
    }

    public java.util.List<EventRegistration> getAllRegistrations() {
        return repository.findAll();
    }
}