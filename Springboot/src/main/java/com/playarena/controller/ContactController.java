package com.playarena.controller;

import com.playarena.entity.ContactMessage;
import com.playarena.repository.ContactRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/api/contact")
@CrossOrigin(origins = "http://localhost:5173")
public class ContactController {

    @Autowired
    private ContactRepository contactRepository;

    @PostMapping
    public ContactMessage saveMessage(@RequestBody ContactMessage message) {
        return contactRepository.save(message);
    }

    @GetMapping
    public List<ContactMessage> getAllMessages() {
        return contactRepository.findAll();
    }
}